# PHP Web Crawler

This is a basic web crawler built in PHP that navigates through web pages starting from a given URL. It extracts metadata such as titles, descriptions, and keywords from the pages it visits.

## Features

- Crawls web pages recursively starting from a given URL.
- Extracts the following metadata:
  - Page Title
  - Meta Description
  - Meta Keywords
- Resolves relative links to absolute URLs.
- Handles edge cases like JavaScript links and duplicate URLs.
- Outputs metadata in JSON format.
- Tracks all visited URLs.

## Prerequisites

- PHP installed on your system (version 7.0 or higher).
- A local or live server to host and test the crawler.

## How to Use

1. Clone the repository or download the script.
2. Place the script in your server's directory (e.g., `htdocs` for XAMPP or similar).
3. Update the `$start` variable with the URL you want to crawl. For example:
   ```php
   $start = "http://example.com";
