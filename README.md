# RIEPSScript

This script was created by Rashard Hinds in November, 2015 using JQuery, CasperJs (https://github.com/n1k0/casperjs), PhantomJs (https://github.com/Medium/phantomjs) to do scraping on server

Script was able to successfully scrape over 40 tasks, zip and email a link to a student within 5 minutes.

History

December 2015, the Rhode Island Electronic Portfolio System (RIEPS) was discontinued and students were responsible for getting out their assignments (aka tasks) and moving them to Google Drive, I was determined to develop a method for extracting a student's assignments.

The method that administration had determined was time consuming an required manually searching through the porfolio, taking the screenshot and saving all assets.  This method took students that chose to do it this way hours.

After various iterations, including a browser add-on, I ended up completing the Phantomjs and CasperJs script that went through a person's RIEPS account, by logging in (via the headless browser), finding tasks they've completed (eliminating any prior to highschool), creates a folder per task, takes a screenshot of the task page and downloads the files from that page and then puts all tasks into a larger centralized folder.  After, it zipped that folder and emailed the link to the student.

The script was a little side projected developed over a two week period in November and when completed, served over 6 dozen stduents.

My goal was to create a faster method.  Using the script, they would submit their information on the frontend website, and wait for the email.  Once they received the email, they would download the file by clicking on the link in the email and then simply dragging and dropping the zip folder into Google Drive.

Project was a success, feel free to check out the code. 

