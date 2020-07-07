# RESTful API

Your task is to create a RESTful API where users can create products and place orders with those products. All orders  should be fulfilled and shipped out from only US based Printing companies.

## User

A User can have many products on their product list and be able to place numerous Orders. A new user can be created via end-point - all users upon creation have $100 in their balance (No need to create authorization and logins).

All Products and Orders  should be linked to Users.

## Product

Your RESTful API should offer  the creation of only two types of products - mugs and t-shirts. Each product should have a Title, Unique SKU (Stock Keeping Unit) and Cost. You should  be able to get a list of products for each  user.

## Order

Your RESTful API  should allow placement of orders with multiple products. The total cost of the order should include shipping and product costs. Orders can be shipped either with Standard or Express shipping. You should be able to get a list of all orders for each user.

## Shipping costs

### Standard

#### Domestic order

Mugs: $2 for the first item and $1 for each additional item in the order.  
T-Shirts: $1 for the first item and $0.50 for each additional item in the order.


#### International orders

Mugs: $5 for the first item and $2.50 for each additional item in the order.  
T-Shirts: $3 for the first item and $1.50 for each additional item in the order.

### Express

Available only for domestic orders. Shipping cost is $10 per item for all products. 

## Address validation

### Domestic orders (within US)

Required:

- Full name  
- Address  
- Country  
- State  
- City
- ZIP  
- Phone  

### International orders

Required:

- Full name  
- Address  
- Country  
- Phone  
- City
  
Optional:

- Region  
- ZIP  

## Order payments

Whenever a new order is placed, the user is charged the total cost from their balance. A new order cannot be created if the user does not have enough balance.

## Technical requirements

- The application has to be written in PHP 7.2+ using the Symfony 4+ framework  
- The application has to be dockerized  
- Application must have tests  
- Github, Gitlab, Bitbucket  

## What gets evaluated

- Are business requirements fulfilled  
- RESTful best practices  
- Software design  
- Tests have to be meaningful and green  
- Readme  

Happy Coding!
