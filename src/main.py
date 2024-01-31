import pyrax
import requests
import csv
import os
from collections import defaultdict

def load_csv(csv_file_path):
    data_by_domain = defaultdict(list)

    with open(csv_file_path, 'r') as csv_file:
        csv_reader = csv.reader(csv_file)

        for row in csv_reader:
            domain, record, record_type = row
            data_by_domain[domain].append({'Record': record, 'Type': record_type})

    return data_by_domain

def get_ip():
    url = "http://ip-api.com/json"
    response = requests.get(url)

    if response.status_code == 200:
        data = response.json()
        ip = data["query"]
        return ip
    else:
        # Handle error, you might want to raise an exception or return a default value
        print("Error fetching IP. Status code:", response.status_code)
        return None

def update_record(domain_name,record,ip_address):
    updated = False
    domain_record = record["Record"]
    domain_type = record["Type"]
    domain = pyrax.cloud_dns.find(name=domain_name)
    try:
        c_record = domain.find_record(name=domain_record,record_type=domain_type)
        if c_record.data != ip_address:
            c_record.update(data=ip_address)
            updated = True
    except:
        print(domain_record+" - Not found")
    return updated

username = os.getenv("RAX_USER")
apiKey = os.getenv("RAX_KEY")

ip_address = get_ip()

csv_file_path = "data/"+os.getenv("RAX_CSV")
data_by_domain = load_csv(csv_file_path)

# print(data_by_domain)

pyrax.set_setting('identity_type', 'rackspace')
pyrax.set_credentials(username, apiKey)

for domain in data_by_domain:
    print(domain)
    for record in data_by_domain[domain]:
        # print(record)
        if update_record(domain,record,ip_address) :
            print(record["Record"]+" - Updated")
        else:
            print(record["Record"]+" - Not updated")