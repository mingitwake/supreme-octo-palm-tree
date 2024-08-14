import os
from azure.core.credentials import AzureKeyCredential
from azure.ai.documentintelligence import DocumentIntelligenceClient
from azure.ai.documentintelligence.models import AnalyzeResult, AnalyzeDocumentRequest
from langchain_core.documents import Document
from langchain_text_splitters import RecursiveCharacterTextSplitter

# Text Splitting
def split(texts, chunk_size=1024, chunk_overlap=128):
    text_splitter = RecursiveCharacterTextSplitter(chunk_size=chunk_size, chunk_overlap=chunk_overlap)
    return text_splitter.split_documents(texts)

def get_words(page, line):
    result = []
    for word in page.words:
        if _in_span(word, line.spans):
            result.append(word)
    return result

# To learn the detailed concept of "span" in the following codes, visit: https://aka.ms/spans 
def _in_span(word, spans):
    for span in spans:
        if word.span.offset >= span.offset and (word.span.offset + word.span.length) <= (span.offset + span.length):
            return True
    return False


def analyze_layout(documentUrl):
    from azure.core.credentials import AzureKeyCredential
    from azure.ai.documentintelligence import DocumentIntelligenceClient
    from azure.ai.documentintelligence.models import AnalyzeResult, AnalyzeDocumentRequest
    
    # For how to obtain the endpoint and key, please see PREREQUISITES above.
    endpoint = os.environ["DI_ENDPOINT"]
    key = os.environ["DI_API_KEY"]

    document_intelligence_client = DocumentIntelligenceClient(endpoint=endpoint, credential=AzureKeyCredential(key))
    
    # # Analyze a document at a URL:
    # formUrl = "https://raw.githubusercontent.com/Azure-Samples/cognitive-services-REST-api-samples/master/curl/form-recognizer/sample-layout.pdf"
    # # Replace with your actual formUrl:
    # # If you use the URL of a public website, to find more URLs, please visit: https://aka.ms/more-URLs 
    # # If you analyze a document in Blob Storage, you need to generate Public SAS URL, please visit: https://aka.ms/create-sas-tokens
    poller = document_intelligence_client.begin_analyze_document(
        "prebuilt-layout",
        AnalyzeDocumentRequest(url_source=documentUrl)
    )       
    
    # If analyzing a local document, remove the comment markers (#) at the beginning of these 8 lines.
    # Delete or comment out the part of "Analyze a document at a URL" above.
    # Replace <path to your sample file>  with your actual file path.
    # path_to_sample_document = "<path to your sample file>"
    # with open(path_to_sample_document, "rb") as f:
    #     poller = document_intelligence_client.begin_analyze_document(
    #         "prebuilt-layout", analyze_request=f, content_type="application/octet-stream"
    #     )
    result: AnalyzeResult = poller.result()

    pages = [""]
    for page in result.pages: pages.append("")

    for paragraph_idx, paragraph in enumerate(result.paragraphs):
        if paragraph.bounding_regions:
            for region in paragraph.bounding_regions:
                pages[region.page_number]+=(f"Paragraph #{paragraph_idx} content: {paragraph.content}, bounding regions: '{region.polygon}'")
                
    # # Analyze tables.
    if result.tables:
        for table_idx, table in enumerate(result.tables):
            if table.bounding_regions:
                for region in table.bounding_regions:
                    pages[region.page_number]+=(f"Table #{table_idx} has {table.row_count} rows and {table.column_count} columns, bounding regions: '{region.polygon}'")
            for cell in table.cells:
                if cell.bounding_regions:
                    for region in cell.bounding_regions:
                        pages[region.page_number]+=(f"..cell[{cell.row_index}][{cell.column_index}], text: '{cell.content}', bounding regions: '{region.polygon}'")

    documents = []
    for page_idx, page in enumerate(pages):
        documents.extend(split([Document(page_content=page, metadata={"source": documentUrl, "page": page_idx})]))
    return documents

if __name__ == "__main__":
    from azure.core.exceptions import HttpResponseError
    from dotenv import find_dotenv, load_dotenv

    # try:
    load_dotenv(find_dotenv())
    print(analyze_layout("https://engg.hku.hk/Portals/0/TPG/FAQ_2024-25_vff.pdf"))
    # except HttpResponseError as error:
    #     # Examples of how to check an HttpResponseError
    #     # Check by error code:
    #     if error.error is not None:
    #         if error.error.code == "InvalidImage":
    #             print(f"Received an invalid image error: {error.error}")
    #         if error.error.code == "InvalidRequest":
    #             print(f"Received an invalid request error: {error.error}")
    #         # Raise the error again after printing it
    #         raise
    #     # If the inner error is None and then it is possible to check the message to get more information:
    #     if "Invalid request".casefold() in error.message.casefold():
    #         print(f"Uh-oh! Seems there was an invalid request: {error}")
    #     # Raise the error again
    #     raise

# Next steps:
# Learn more about Layout model: https://aka.ms/di-layout
# Find more sample code: https://aka.ms/doc-intelligence-samples