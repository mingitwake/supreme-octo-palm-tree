import chromadb
from langchain_core.documents import Document
from langchain_openai import AzureChatOpenAI, AzureOpenAIEmbeddings
from langchain_chroma import Chroma
from langchain_core.messages import SystemMessage
from langchain_core.prompts import HumanMessagePromptTemplate, ChatPromptTemplate
from document_loader import read_urls
from chromadb.config import DEFAULT_TENANT, DEFAULT_DATABASE, Settings

# Configuration
from configurations import ( 
    MODEL_DEPLOYMENT, 
    EMBEDDING_DEPLOYMENT,
    API_KEY,
    API_VERSION,
    AZURE_ENDPOINT, 
    )

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
    HumanMessagePromptTemplate.from_template("Query: {query}\n Content: {content}\n Answer: "),
])

COLLECTION_NAME = "Administration"

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
        DUMMY_DOCUMENT = Document(page_content="dummy", metadata={"source": ""})
        self.index = Chroma.from_documents(
            documents=[DUMMY_DOCUMENT], 
            embedding=self.embedding, 
            client=self.client, 
            collection_name=self.collection_name,)

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
    
    def get_response(self, model, prompt, query):
        return (prompt | model).invoke({"content": self.retriever.invoke(query), "query": query}).content
    
    def clear(self):
        self.client.delete_collection(self.collection_name)

# Main Function
if __name__ == "__main__":
    # Load Documents
    urls_to_read = [
        "https://engg.hku.hk/Admissions/MSc/Fees",
        "https://engg.hku.hk/Admissions/MSc/Entrance-Requirements",
        "https://engg.hku.hk/Admissions/MSc/FAQ",
    ]
    documents = read_urls(urls_to_read)
    
    # Create Wrapper
    wrapper = ChromaWrapper(COLLECTION_NAME, EMBEDDING, True)
    
    # Setup Wrapper
    wrapper.create_index()
    wrapper.add_documents(documents)
    
    # Get Response
    wrapper2 = ChromaWrapper(COLLECTION_NAME, EMBEDDING, False)
    wrapper2.get_index()
    wrapper2.setup_retriever()
    query = input("Enter: ")
    print(wrapper2.get_response(MODEL, PROMPT, query))

    # Clear
    wrapper.clear()
    exit()