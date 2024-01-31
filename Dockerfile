FROM python:3.9

RUN mkdir -p /app
WORKDIR /app
COPY src/* .
RUN pip install -r requirements

ENV RAX_CSV='domains.csv'

CMD [ "python" , "main.py" ]