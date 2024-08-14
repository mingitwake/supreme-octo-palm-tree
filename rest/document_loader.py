import requests, os
from bs4 import BeautifulSoup 
from langchain_core.documents import Document
from langchain_text_splitters import RecursiveCharacterTextSplitter

# Text Splitting
def split(texts, chunk_size=1024, chunk_overlap=128):
    text_splitter = RecursiveCharacterTextSplitter(chunk_size=chunk_size, chunk_overlap=chunk_overlap)
    return text_splitter.split_documents(texts)

# Reading URL and Processing Content
def read_url(url, id):
    documents = []
    response = requests.get(url)
    response.raise_for_status()
    soup = BeautifulSoup(response.text, 'html.parser')
    for tag in soup(['script', 'style', 'header', 'footer']):
        tag.decompose()
    content = '\n'.join(soup.stripped_strings)
    documents.extend(split([Document(page_content=content, metadata={"source": url, "id": id})]))
    return documents

def read_file(filepath, id):
    from azure.core.credentials import AzureKeyCredential
    from azure.ai.documentintelligence import DocumentIntelligenceClient
    from azure.ai.documentintelligence.models import AnalyzeResult, AnalyzeDocumentRequest
    from dotenv import load_dotenv 
    load_dotenv() 
    
    # For how to obtain the endpoint and key, please see PREREQUISITES above.
    endpoint = os.environ["DI_ENDPOINT"]
    key = os.environ["DI_API_KEY"]
    filepath = "/var/www/laravel/storage/app/"+filepath

    document_intelligence_client = DocumentIntelligenceClient(endpoint=endpoint, credential=AzureKeyCredential(key))
    
    with open(filepath, "rb") as f:
        poller = document_intelligence_client.begin_analyze_document(
            "prebuilt-layout", analyze_request=f, content_type="application/octet-stream"
        )
    result: AnalyzeResult = poller.result()

    pages = [""]
    for page in result.pages: pages.append("")

    for paragraph_idx, paragraph in enumerate(result.paragraphs):
        if paragraph.bounding_regions:
            for region in paragraph.bounding_regions:
                pages[region.page_number]+=(f"Paragraph #{paragraph_idx} content: {paragraph.content}, bounding regions: '{region.polygon}'\n\n")
                
    # # Analyze tables.
    if result.tables:
        for table_idx, table in enumerate(result.tables):
            if table.bounding_regions:
                for region in table.bounding_regions:
                    pages[region.page_number]+=(f"Table #{table_idx} has {table.row_count} rows and {table.column_count} columns, bounding regions: '{region.polygon}'\n\n")
            for cell in table.cells:
                if cell.bounding_regions:
                    for region in cell.bounding_regions:
                        pages[region.page_number]+=(f"..cell[{cell.row_index}][{cell.column_index}], text: '{cell.content}', bounding regions: '{region.polygon}'\n\n")

    documents = []
    for page_idx, page in enumerate(pages):
        documents.extend(split([Document(page_content=page, metadata={"source": filepath, "page": page_idx, "id": id})]))

    return documents

def read_file_url(url, id):
    from azure.core.credentials import AzureKeyCredential
    from azure.ai.documentintelligence import DocumentIntelligenceClient
    from azure.ai.documentintelligence.models import AnalyzeResult, AnalyzeDocumentRequest
    from dotenv import load_dotenv 
    load_dotenv() 
    
    # For how to obtain the endpoint and key, please see PREREQUISITES above.
    endpoint = os.environ["DI_ENDPOINT"]
    key = os.environ["DI_API_KEY"]

    document_intelligence_client = DocumentIntelligenceClient(endpoint=endpoint, credential=AzureKeyCredential(key))

    poller = document_intelligence_client.begin_analyze_document(
        "prebuilt-layout",
        AnalyzeDocumentRequest(url_source=url)
    )
    result: AnalyzeResult = poller.result()

    pages = [""]
    for page in result.pages: pages.append("")

    for paragraph_idx, paragraph in enumerate(result.paragraphs):
        if paragraph.bounding_regions:
            for region in paragraph.bounding_regions:
                pages[region.page_number]+=(f"Paragraph #{paragraph_idx} content: {paragraph.content}, bounding regions: '{region.polygon}'\n\n")
                
    # # Analyze tables.
    if result.tables:
        for table_idx, table in enumerate(result.tables):
            if table.bounding_regions:
                for region in table.bounding_regions:
                    pages[region.page_number]+=(f"Table #{table_idx} has {table.row_count} rows and {table.column_count} columns, bounding regions: '{region.polygon}'\n\n")
            for cell in table.cells:
                if cell.bounding_regions:
                    for region in cell.bounding_regions:
                        pages[region.page_number]+=(f"..cell[{cell.row_index}][{cell.column_index}], text: '{cell.content}', bounding regions: '{region.polygon}'\n\n")

    documents = []
    for page_idx, page in enumerate(pages):
        documents.extend(split([Document(page_content=page, metadata={"source": url, "page": page_idx, "id": id})]))
    return documents