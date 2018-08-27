Spreadsheet Translator Google Drive Provider with Authentication
========================

This package grasps a protected spreadsheet document from google drive with authentication


How to create Google Drive Credentials
------------


1) Create a project (https://console.developers.google.com). You will need a Google account.
2) Enable the Google Sheets API (https://developers.google.com/sheets/api/quickstart/php)
3) Download the Client Configuration file (credentials.json) in a private folder 
4) First time you run the command via a terminal, a url will be shown, this url must be placed on a browser, allow Google permissions, and copy and paste the given code into the terminal.

<!-- Log into your google account
Go to the url: https://console.developers.google.com 
Go to the Google Apis and create a new project
Select the already created project in the dropdown up in the Dashboard, you might wait for a few seconds for the new project to be created and to appear in the dropdown.  
Create a Service Account and download the json file. This file will be the value for the configuration entry named as client_secret_path.
Create an empty file called credentials.json. This is where the token will be generated.

Grant permissions to Google Sheets API accessing to this url: https://console.developers.google.com/apis/api/sheets.googleapis.com/overview?project=924017156235
-->


Related
------------

  - <a href="https://github.com/samuelvi/spreadsheet-translator-core">Core Bundle</a>
  - <a href="https://github.com/samuelvi/spreadsheet-translator-symfony-bundle">Symfony Bundle</a>


Requirements
------------

  * PHP >=5.5.9
  * Symfony ~2.3|~3.0



Contributing
------------

We welcome contributions to this project, including pull requests and issues (and discussions on existing issues).

If you'd like to contribute code but aren't sure what, the issues list is a good place to start. If you're a first-time code contributor, you may find Github's guide to <a href="https://guides.github.com/activities/forking/">forking projects</a> helpful.

All contributors (whether contributing code, involved in issue discussions, or involved in any other way) must abide by our code of conduct.

License
-------

Spreadsheet Translator Symfony Bundle is licensed under the MIT License. See the LICENSE file for full details.