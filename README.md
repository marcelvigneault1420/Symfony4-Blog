# Symfony4-Blog
A Symfony blog
## Introduction
This is a website where you can connect, add a post, see everyone post, order posts by tags / categories, leave comments or like a post, etc.

This is a dummy website to learn the Symfony framework and keep an example of a symfony framework project.
## Tech used
- Frontend
  - HTML5
  - CSS3
    - CSS Grid, Flexbox, Transform, Animations
  - JQuery
- Template engine
  - Twig
- Backend PHP framework
  - Symfony 4
- Database
  - MariaDB
  - ORM
    - Doctrine
- KANBAN
  - Trello (https://trello.com/b/GlwK1yBY)
- Server
  - **_TODO_**

## Things I learned
- slug
  - Using slug in links instead of ID is better for stats. It doesn't give information to the user about how much people are subscribed to your website, how you auto-increment, how frequent someone post something.
- CSRF
  - Using a CSRF token can protect you against bruteforce attacks. I used them for my delete page, not just for my forms.
- Theme-color meta tag
  - I didn't knew this tag exist. This tag let you have a custom color for your tab on mobile. It's also required for PWA websites.
- Using ::after in CSS
  - I used ::after to create a fade out effect on the posts that are too long. I used a gradient from transparent to white.
