# Idpay payment gateway

Payment gateway for Iranian website

![MySQL](data:image/png;base64,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)

## How to install:

- 1-copy files in your theme and plugin folder

## Description

You can download idpay payment gateway at https://idpay.ir/

## Questions

If you have any questions about this opinionated list, do not hesitate to contact me [@MonicaMl01](https://twitter.com/MonicaMl01) on Twitter or open an issue on GitHub.