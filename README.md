# PROJECT IS FULL OF SEC VULNS AND BUGS!!!! THIS CODE WAS PUBLISHED SO PEOPLE CAN LEARM HOW TO CODE. DO NOT USE THIS CODE IN PRODUCTION
# CODE DOES NOT REPRESENT VKE, INTERNET.SYSTEMS OR MAPLER'S CURRENT CODE, SECURITY, ECT.
# THIS CODE IS AND NEVER WILL BE MAINTAINED

# VKE Twitter Clone
This repository contains the code for a Twitter clone web application developed by VKE + contributors.

## Introduction
his project is a Twitter clone web application called "VKE" that aims to replicate some of the basic functionalities of Twitter. It allows users to create posts, view posts from other users, and perform various interactions such as watching YouTube and viewing Mastodon posts.

## Installation
To run the VKE on your local machine, follow these steps:

1.
```
git clone https://github.com/vkeapp/old-website.git
cd old-website
```
2. Create a new MySQL database and import the provided SQL dump file `database.sql`.
3. Update the database connection details in the files and other relevant files with your MySQL database credentials.
4. Make sure you have PHP and a web server (e.g., Apache or Nginx) installed on your system.
5. Start your web server and visit the website in your web browser.

## Code Explanation
he codebase is written in PHP and MySQL, utilizing Bootstrap for styling. Here's a brief overview of some of the main files and their functionalities:

* `index.php`: This file is the landing page of the application. It handles user authentication and redirects to the main tweets page.
* `new.php`: This page displays the list of tweets posted by users. Users can create new tweets on this page as well.
* `profile.php`: This page displays the profile of a user, including their tweets.
* `panel.php`: If the logged-in user is a staff member, they can access this page to perform staff-specific actions.

## Project Status
This project is no longer actively maintained or in use. It was developed as a learning exercise and proof of concept for building a simple Twitter-like web application using PHP and MySQL.
