# GoogleCalendarPlugin
The Google Calendar Plugin is a event creator that makes events using the Google Calendar API. The main function of the plugin is to add an option which send events of the activity to all students of the course. This option is available in every activity on moodle 



## Required version of Moodle

This version works with Moodle 3.7 and above.

## Requirements Before installing the plugin

1. Activate OAuth2 to Login with Google

    * Login as an administrator and put Moodle in 'Maintenance Mode'.
    * Visit site administration > Plugins >  Authentication > Manage authentication 

![imagen](https://user-images.githubusercontent.com/37383745/173208665-dec2379f-7288-46d9-96ac-a0408d331f00.png)

2. Create Google OAuth 2 Services

    * Login as an administrator and put Moodle in 'Maintenance Mode'
    * Visit site administration >  Server > OAuth 2 services 
  
    *Remember you need the keys from the google cloud services*

![imagen](https://user-images.githubusercontent.com/37383745/173208599-0ee210d6-673c-481c-a796-cea21aa3b2e5.png)


## Requirements After installing the plugin

1. Provide for the plugin the name of the OAuth 2 Google Service

    * Login as an administrator and put Moodle in 'Maintenance Mode'
    * Visit Plugins > Local plugins > Google Calendar Events

![imagen](https://user-images.githubusercontent.com/37383745/173208936-f6b742ca-52bd-47cf-a5a1-134c6839a80e.png)


## How to Use

For some activies like assignment which already contain dates for start and end. The plugin use this dates.  

![imagen](https://user-images.githubusercontent.com/37383745/173209043-21c60028-9f71-4bc8-8550-b1d2434a27bf.png)

So the only input that show it's going to be the checkbox

![imagen](https://user-images.githubusercontent.com/37383745/173209135-8f7b9f64-746d-468d-ab6c-18c74914b23b.png)

But for others like the activity *choice* the plugin add inputs for this dates.

![imagen](https://user-images.githubusercontent.com/37383745/173209110-c2d395f1-f5b3-485f-9301-d93908fddc13.png)
