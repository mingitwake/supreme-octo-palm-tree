import os, chromadb
from langchain_core.documents import Document
from langchain_openai import AzureChatOpenAI, AzureOpenAIEmbeddings
from langchain_chroma import Chroma
from langchain_core.messages import SystemMessage
from langchain_core.prompts import HumanMessagePromptTemplate, ChatPromptTemplate
from chromadb.config import DEFAULT_TENANT, DEFAULT_DATABASE, Settings

from dotenv import load_dotenv
load_dotenv() 

MODEL_DEPLOYMENT=os.getenv("MODEL_DEPLOYMENT")
EMBEDDING_DEPLOYMENT=os.getenv("EMBEDDING_DEPLOYMENT")
API_KEY=os.getenv("API_KEY")
API_VERSION=os.getenv("API_VERSION")
AZURE_ENDPOINT=os.getenv("AZURE_ENDPOINT")

# Azure OpenAI Model Configuration
MODEL = AzureChatOpenAI(
    azure_deployment=MODEL_DEPLOYMENT,
    azure_endpoint=AZURE_ENDPOINT,
    api_key=API_KEY,
    api_version=API_VERSION,
    temperature=0.2,
    max_tokens=256,
)

EMBEDDING = AzureOpenAIEmbeddings(
    api_key=API_KEY,
    azure_endpoint=AZURE_ENDPOINT,
    azure_deployment=EMBEDDING_DEPLOYMENT,
    openai_api_version=API_VERSION,
)

# Prompt Template
PROMPT = ChatPromptTemplate.from_messages([
    SystemMessage(content =
        '''
        You are EEE-BOT, an administrative assistant at the HKU Department of Electrical and Electronic Engineering (EEE) Admissions Office. 
        Your task is to answer admission-related or EEE-department-related queries in a friendly, concise, and accurate manner. 
        Limit your responses to 250 tokens and reply only in English. Provide only verified information. Always include sources at the end of the response.

        **Edge Case Handling**:
        1. **Ambiguous Queries**: Ask the user for clarification if the query is unclear.
        2. **Off-Topic Queries**: Politely redirect the user to the appropriate department or resource.
        3. **Unsolvable Queries**: Acknowledge the limitation and suggest the user seek human assistance.

        **Handling Concerning Conditions**:
        1. Acknowledge the user's concern.
        2. State your limitations as an automated assistant.
        3. Provide appropriate resources from the list provided.

        **Resources**:
        # General Medical Consultation:
        University Health Service (UHS), tel. : (852) 2549 4686, addr. : 2/F, Meng Wah Complex, online booking: https://uhs4.hku.hk:8443/CMS3/webBooking/main;
        # Counselling & Psychological Services:
        The Samaritan Befrienders Hong Kong, 24-hour hotline: (852) 2389 2222, online chat service: https://chatpoint.org.hk/?language=en;
        Counselling and Person Enrichment Section (CoPE), CEDARS, tel. : (852) 3917 8388, addr. : Room 301-323, 3/F, Main Building, non-urgent appointment: https://www.cedars.hku.hk/cope/cps/appointment
        # Emergencies:
        Police, Fire Services, or Ambulance, tel. : 999; nearest A&E: Queen Mary Hospital.

        **Response Formatting**:
        - Format your response as a one-line dictionary with the keys "message" and "class". Do not use newline characters within the dictionary.
        - The "message" should be the response to the user's query.
        - The "class" should categorize the query as one of the following: "MScFees", "MScCourses", "GeneralInformation", "MScApplication", "MScEntranceRequirement", "Accommodation", "Finance", "Healthcare", "StudentSupport", "Academic", "Visa", "Others", or "Irrelevant".
        - Prioritize the main concern if the query fits multiple categories.
        '''),
    HumanMessagePromptTemplate.from_template(
        '''
        Query: {query}\n
        Histories: {histories}\n
        Sources: {content}\n
        Answer: 
        '''),
])

class ChromaWrapper:
    def __init__(self, collection_name, embedding, reset=False):
        self.client = chromadb.HttpClient(
            host="localhost",
            port=8000,
            ssl=False,
            headers=None,
            settings=Settings(allow_reset=True),
            tenant=DEFAULT_TENANT,
            database=DEFAULT_DATABASE,
        )
        self.collection_name = collection_name
        self.embedding = embedding
        self.index = None
        self.retriever = None
        if reset: self.client.reset()
    
    def create_index(self):
        DUMMY_DOCUMENT = Document(page_content="", metadata={"source": "", "id": ""})
        self.index = Chroma.from_documents(
            documents=[DUMMY_DOCUMENT], 
            embedding=self.embedding, 
            client=self.client, 
            collection_name=self.collection_name,
            )

    def get_index(self):
        self.index = Chroma(
            embedding_function=self.embedding, 
            client=self.client, 
            collection_name=self.collection_name,
            create_collection_if_not_exists=False,
            )

    def add_documents(self, documents):
        self.index.add_documents(documents=documents)
    
    def setup_retriever(self, score_threshold=0.6, k=6):
        self.retriever=self.index.as_retriever(
            search_type="similarity_score_threshold",
            search_kwargs={"score_threshold": score_threshold, "k": k}
        )
    
    def get_response(self, query, histories="", model=MODEL, prompt=PROMPT):
        return (prompt | model).invoke({"content": self.retriever.invoke(query), "histories": histories, "query": query}).content
    
    def clean(self):
        self.client.delete_collection(self.collection_name)
        
def create(collection_name, embedding=EMBEDDING):
    creater = ChromaWrapper(embedding=embedding, collection_name=collection_name, reset=True)
    creater.create_index()

def write(collection_name, documents, embedding=EMBEDDING):
    writer = ChromaWrapper(embedding=embedding, collection_name=collection_name)
    writer.get_index()
    writer.add_documents(documents=documents)
        
def read(collection_name, query, histories, embedding=EMBEDDING, model=MODEL, prompt=PROMPT):
    reader = ChromaWrapper(embedding=embedding, collection_name=collection_name)
    reader.get_index()
    reader.setup_retriever()
    return(reader.get_response(query=query, histories=histories))

def clean(collection_name, embedding=EMBEDDING):
    cleaner = ChromaWrapper(collection_name, embedding)
    cleaner.clean()

def delete(collection_name, id, embedding=EMBEDDING):
    writer = ChromaWrapper(embedding=embedding, collection_name=collection_name)
    writer.get_index()
    ids = writer.index.get(where={"id":id}, include=[])["ids"]
    if len(ids): writer.index.delete(ids=ids)

def show(collection_name, embedding=EMBEDDING):
    writer = ChromaWrapper(embedding=embedding, collection_name=collection_name)
    writer.get_index()
    return(writer.index.get())

