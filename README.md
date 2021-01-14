# NPSbot
Slack NPS manager with new slack API


 Simple NPS notifications integration using Slack Events and Web APIs
 
 For all api methods, refer to https://api.slack.com/

  @author  Laurynas secret <secret.secret@secret.com>
  @version  0.0.15
  
  Functions: 
  1. Enable notifications forever
  2. Enable notifiations for 8 hours (1 shift)
  3. Enable notifications for others (8 hours - 1 shift limit)
  4. Tracks whether anyone posts a message with the following format:
       NPS - @name @name
       limit - 5 symbols (2 names max)
  5. Admin panel with /adminpanel command
