# Rackspace Cloud DNS as a Dynamic DNS
![build status](https://github.com/peanutsguy/raxdyndns/actions/workflows/docker-image.yml/badge.svg)

**IMPORTANT! It has been rewritten from PHP to Python 3.9 (last version working with pyrax)**

This container image can be used to update Rackspace Cloud DNS with the current public IP from where the container is running.

Rackspace API limits must be considered, in order to avoid getting rejected requests.

## Usage
```docker
docker run -d -e RAX_USER={rax_user} -e RAX_APIKEY={rax_api_key} -v {domains_file_folder}:/dyndns/data/domains.csv --name raxdyndns  ghcr.io/peanutsguy/raxdyndns
```

| Parameter | Description |
| - | - |
| -e RAX_USER={rax_user} | Rackspace username |
| -e RAX_APIKEY={rax_api_key} | Rackspace API key |
| -v {domains.csv_file_location}:/app/data/domains.csv | File containing the records that have to be updated |

### domains.csv
This CSV must follow the format described below.

**IMPORTANT! - Only type A records have been tested**
```csv
domain.com,sub.domain.com,record_type
```
#### Example
The following example will update the subdomain dev.braintrust.com.mx with the current public IP.
```csv
braintrust.com.mx,dev.braintrust.com.mx,A
```

[GitHub repository](https://github.com/peanutsguy/raxdyndns)