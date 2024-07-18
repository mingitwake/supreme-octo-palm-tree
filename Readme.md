# Activate virtual environment

# Windows
./.venv/scripts/activate
set FLASK_ENV=development

# Install packages
pip install -r requirements.txt

# Run Chroma Server
docker compose up
http://localhost:8000/api/v1
http://localhost:8000/api/v1/collections

# References
https://docs.docker.com/compose/compose-application-model/
