Developer:

Steve Callan

4/29/11

-----------------------------------

Description:

Access a users YouTube feed easily through ExpressionEngine without needing to use OAuth.

-----------------------------------

Installation Instructions:

- Upload the /youtubee folder to your system/expressionengine/third_party folder

-----------------------------------

Google API Key:

Starting in April 2015, Google requires an API Key in order to query YouTube
data, so this add-on needs an API Key. To get this key, follow the following
instructions.

1.  Navigate to [console.developers.google.com][googleconsole]
2.  Create a project
    * Select "Create a project..." from the "Select a project" dropdown
    * Name the project something reasonable
    * Select "Create"
3.  Enable the YouTube Data API for the project
    * Select "APIs & Auth" -> "APIs" in the sidebar
    * Select on "YouTube Data API"
    * Select on "Enable"
4. Create an API Key
    * Select "APIs & Auth" -> Credentials" in the sidebar
    * Select "Create new Key"
    * Select "Browser Key"
    * _(Optional)_ Enter the URL of the ExpressionEngine control pannel in the
      HTTP referrers for security
    * Select "Create"
5. Add the API Key to ExpressionEngine's config
    * Copy the new API Key
    * If you use [master config][masterconfig], put
      `$env_config['youtubee:googleapikey'] = 'your new api key';` in
      config.master.php
    * If you do not use master config, put
      `$config['youtubee:googleapikey'] = 'your new api key';` in
      system/expressionengine/config/config.php

-----------------------------------

**User Guide**

    {exp:youtubee:entries user="YOUR_YOUTUBE_USERNAME" limit="HOW_MANY_ENTRIES_TO_SHOW" key="YOU_TUBE_VIDEO_ID"}

    <article>
    <h3>{title}</h3>
    <p>{short_description}</p>
    </article>

    {/exp:youtubee:entries}

**Parameters**

user: the username of the feed you would like to pull (required)

limit: Integer of how many items to show (not required)

key: If you would like to filter the results by a certain video user this parameter.  To retreive multiple videos separate by | (not required)

**Variables**

{title} - The title of the video

{short_description} - The short description of the video

{image} - thumbnail of the video

{views} - The number of views this video has

{time} - The total length of the video

{url} - The YouTube Video Link of the video

{key} - The unique identifier for this video

[googleconsole]: https://console.developers.google.com
[masterconfig]: https://github.com/focuslabllc/ee-master-config
