# Worksome Exports Micro-service

[![Tests](https://github.com/worksome/exports-service/actions/workflows/main.yml/badge.svg)](https://github.com/worksome/exports-service/actions/workflows/main.yml)
[![Code Analysis](https://github.com/worksome/exports-service/actions/workflows/code-analysis.yml/badge.svg)](https://github.com/worksome/exports-service/actions/workflows/code-analysis.yml)

## Create Export

Example request to generate an export. Export is then sent via email.

```gql
mutation {
  createExport(input: {
    type: CONTRACT
    args: {
      companies: [67392, 67501, 67518, 64256, 61690, 76245]
      dateFrom: "2021-06-01"
      dateTo: "2021-06-03"
    }
    userId: 1
    accountId: 1
    accountType: "company"
    delivery: {
      type: EMAIL
      value: "lukas@worksome.com"
    }
  }) {
    status
    message
  }
}
```
