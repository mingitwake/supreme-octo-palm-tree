import requests, os
from bs4 import BeautifulSoup 
from langchain_core.documents import Document
from langchain_text_splitters import RecursiveCharacterTextSplitter

# Text Splitting
def split(texts, chunk_size=1024, chunk_overlap=128):
    text_splitter = RecursiveCharacterTextSplitter(chunk_size=chunk_size, chunk_overlap=chunk_overlap)
    return text_splitter.split_documents(texts)

# Reading URLs and Processing Content
def read_urls(urls, ids):
    documents = []
    for url, id in zip(urls, ids):
        response = requests.get(url)
        response.raise_for_status()
        soup = BeautifulSoup(response.text, 'html.parser')
        for tag in soup(['script', 'style', 'header', 'footer']):
            tag.decompose()
        content = '\n'.join(soup.stripped_strings)
        documents.extend(split([Document(page_content=content, metadata={"source": url, "id": id})]))
    return documents

def read_files(filepath):
    from azure.core.credentials import AzureKeyCredential
    from azure.ai.documentintelligence import DocumentIntelligenceClient
    from azure.ai.documentintelligence.models import AnalyzeResult, AnalyzeDocumentRequest
    from dotenv import load_dotenv 
    load_dotenv() 
    
    # For how to obtain the endpoint and key, please see PREREQUISITES above.
    endpoint = os.environ["DI_ENDPOINT"]
    key = os.environ["DI_API_KEY"]

    document_intelligence_client = DocumentIntelligenceClient(endpoint=endpoint, credential=AzureKeyCredential(key))
    
    with open(filepath, "rb") as f:
        poller = document_intelligence_client.begin_analyze_document(
            "prebuilt-layout", analyze_request=f, content_type="application/octet-stream"
        )
    result: AnalyzeResult = poller.result()

    content = ""
    
    if result.styles and any([style.is_handwritten for style in result.styles]):
        content+=("Document contains handwritten content\n\n")
    else:
        content+=("Document does not contain handwritten content\n\n")

    for paragraph_idx, paragraph in enumerate(result.paragraphs):
        content += f"Paragraph #{paragraph_idx} content: '{paragraph.content}'\n\n"
        if paragraph.bounding_regions:
            for region in paragraph.bounding_regions:
                content += f"bounding regions: page '{region.page_number}', polygon '{region.polygon}'\n\n"

    if result.tables:
        for table_idx, table in enumerate(result.tables):
            content += f"Table #{table_idx} has {table.row_count} rows and {table.column_count} columns\n\n"
            if table.bounding_regions:
                for region in table.bounding_regions:
                    content += f"bounding regions: page '{region.page_number}', polygon '{region.polygon}'\n\n"
            for cell in table.cells:
                content += f"..cell[{cell.row_index}][{cell.column_index}], text '{cell.content}'\n\n"
                if cell.bounding_regions:
                    for region in cell.bounding_regions:
                        content += f"..bounding regions: page '{region.page_number}', polygon '{region.polygon}'\n\n"
    
    documents = []
    documents.extend(split([Document(page_content=content, metadata={"source": filepath, "id": ""})]))
    return documents