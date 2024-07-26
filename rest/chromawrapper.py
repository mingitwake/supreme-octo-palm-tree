import os, chromadb
from langchain_core.documents import Document
from langchain_openai import AzureChatOpenAI, AzureOpenAIEmbeddings
from langchain_chroma import Chroma
from langchain_core.messages import SystemMessage
from langchain_core.prompts import HumanMessagePromptTemplate, ChatPromptTemplate
from chromadb.config import DEFAULT_TENANT, DEFAULT_DATABASE, Settings

from dotenv import load_dotenv, dotenv_values 
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
    SystemMessage(content="You are Jenny, an administrative assistant at HKU. Provide concise and accurate responses. Only include information you are certain about."),
    HumanMessagePromptTemplate.from_template("Query: {query}\n Source: {content}\n Answer: "),
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
        DUMMY_DOCUMENT = Document(page_content="", metadata={"source": ""})
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
    
    def get_response(self, query, model=MODEL, prompt=PROMPT):
        return (prompt | model).invoke({"content": self.retriever.invoke(query), "query": query}).content
    
    def clean(self):
        self.client.delete_collection(self.collection_name)
        
def create(collection_name, embedding=EMBEDDING):
    creater = ChromaWrapper(embedding=embedding, collection_name=collection_name)
    creater.create_index()

def write(collection_name, documents, embedding=EMBEDDING):
    writer = ChromaWrapper(embedding=embedding, collection_name=collection_name)
    writer.get_index()
    writer.add_documents(documents=documents)
        
def read(collection_name, query, embedding=EMBEDDING, model=MODEL, prompt=PROMPT):
    reader = ChromaWrapper(embedding=embedding, collection_name=collection_name)
    reader.get_index()
    reader.setup_retriever()
    return(reader.get_response(query=query))

def clean(collection_name, embedding=EMBEDDING):
    cleaner = ChromaWrapper(collection_name, embedding)
    cleaner.clean()