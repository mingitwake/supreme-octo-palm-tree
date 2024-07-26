import requests
from bs4 import BeautifulSoup 
from langchain_core.documents import Document
from langchain_text_splitters import RecursiveCharacterTextSplitter
from langchain_community.document_loaders import PyPDFLoader

# Text Splitting
def split(texts, chunk_size=1024, chunk_overlap=128):
    text_splitter = RecursiveCharacterTextSplitter(chunk_size=chunk_size, chunk_overlap=chunk_overlap)
    return text_splitter.split_documents(texts)

# Reading URLs and Processing Content
def read_urls(urls):
    documents = []
    for url in urls:
        try:
            response = requests.get(url)
            response.raise_for_status()
            soup = BeautifulSoup(response.text, 'html.parser')
            for tag in soup(['script', 'style', 'header', 'footer']):
                tag.decompose()
            content = '\n'.join(soup.stripped_strings)
            documents.extend(split([Document(page_content=content, metadata={"source": url})]))
        except requests.RequestException as e:
            print(f"Error fetching {url}: {e}")
    return documents