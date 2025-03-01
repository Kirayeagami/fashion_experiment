# Project Brief: K I R A Fashion E-commerce Website

## Project Overview
K I R A is a fashion e-commerce website designed to provide users with a seamless shopping experience. The platform allows users to browse, search, and purchase a variety of fashion products.

## Purpose
The primary goal of the K I R A project is to create an online shopping platform that is user-friendly, visually appealing, and functional. It aims to cater to fashion enthusiasts by offering a wide range of products and features.

## Technologies Used
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Frameworks/Libraries**: None specified, but custom scripts are used for functionality.

## File Structure
- **pages/**: Contains all the main pages of the website (e.g., about, collections, contact).
- **assets/**: Holds all static assets, including CSS stylesheets, JavaScript files, and images.
  - **css/**: Stylesheets for various pages and components.
  - **js/**: JavaScript files for interactivity and functionality.
  - **images/**: Product images and other visual assets.
- **includes/**: Contains reusable components such as the header and footer, which are included in various pages.
- **admin/**: Contains files related to admin functionalities, such as managing products and users.
- **seller/**: Contains files for seller-specific functionalities, such as adding and editing products.
- **user/**: Contains files for user-related functionalities, such as login, registration, and profile management.

## Key Features
- User authentication (login, registration)
- Product listings and details
- Admin dashboard for managing products and users
- Seller functionalities for adding and editing products
- Newsletter subscription feature
- Responsive design for mobile and desktop users

## How It Works
1. **User Interaction**: Users can navigate through the website, view products, and add them to their cart.
2. **Admin and Seller Management**: Admins can manage users and products, while sellers can add and edit their products.
3. **Database Integration**: The website interacts with a MySQL database to store user information, product details, and order history.
4. **Reusable Components**: The header and footer are included in multiple pages to maintain consistency across the site.

## Conclusion
The K I R A project is a comprehensive e-commerce solution that combines various technologies and features to provide a robust shopping experience. It is designed to be scalable and maintainable, allowing for future enhancements and updates.


# For ***DATABASE*** i use MySql
--------------------------------

# in This project there is the DB name is fashion.
# Tables are :-
        * users
        * sellers
        * products
        * specifications
        * cart
        * product_keywords
        * ShippingInfo
        * PaymentInfo

# Table CREATE command :

## users :-
        CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        role ENUM('admin', 'seller', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
## sellers :-
        **
## product :-
        CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uploder_email VARCHAR(255) NOT NULL,
        product_name VARCHAR(255) NOT NULL,
        product_description TEXT NOT NULL,
        categories VARCHAR(255) NOT NULL,
        payment_type VARCHAR(50) NOT NULL,
        product_total_stock INT NOT NULL,
        refundable VARCHAR(50) NOT NULL,
        product_max_price VARCHAR(50) NOT NULL,
        product_min_price VARCHAR(50) NOT NULL,
        product_pickup_address VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
## specifications :-
        CREATE TABLE specifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        spec_name VARCHAR(255) NOT NULL,
        spec_description TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        );
## product_keywords :-
        create table product_keywords (
        id int auto_increment primary key,
        product_id int not null,
        keywords varchar(255),
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        );
## cart :-
        create table cart(
        id int auto_increment primary key,
        user_email varchar(255) not null,
        user_type varchar(255) not null,
        product_id int not null ,
        product_quantity int not null default 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
## ShippingInfo :-
        CREATE TABLE ShippingInfo (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_email varchar(255) not null,
        fullName VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phoneNumber VARCHAR(20) NOT NULL,
        address TEXT NOT NULL,
        pinCode VARCHAR(10) NOT NULL
        );
## PaymentInfo :- 
        CREATE TABLE PaymentInfo (
        id INT PRIMARY KEY AUTO_INCREMENT,
        user_email varchar(255) not null,
        cardNumber VARCHAR(16) NOT NULL,
        cardholderName VARCHAR(255) NOT NULL,
        expiryDate VARCHAR(7) NOT NULL, -- Format: MM/YYYY
        cvv VARCHAR(3) NOT NULL
        );

        
"# fashion_experiment" 
