<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Borrowing System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            width: 100%;
            max-width: 1000px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .header {
            background: #2c3e50;
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .authors {
            font-style: italic;
            margin-bottom: 15px;
            color: #ecf0f1;
        }
        
        .book-container {
            display: flex;
            padding: 30px;
            gap: 30px;
        }
        
        .book-cover {
            flex: 1;
            text-align: center;
        }
        
        .book-cover img {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .book-details {
            flex: 2;
        }
        
        .book-title {
            font-size: 2rem;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        
        .book-info {
            margin-bottom: 25px;
            line-height: 1.6;
            color: #34495e;
        }
        
        .status {
            display: inline-block;
            padding: 5px 15px;
            background: #2ecc71;
            color: white;
            border-radius: 20px;
            font-weight: 500;
            margin-bottom: 20px;
        }
        
        .rating-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
        }
        
        .rating-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: #2c3e50;
        }
        
        .stars {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .star {
            color: #ddd;
            font-size: 2rem;
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .star:hover,
        .star.active {
            color: #f1c40f;
        }
        
        .borrow-btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .borrow-btn:hover {
            background: #2980b9;
        }
        
        .borrow-btn.returned {
            background: #2c3e50;
        }
        
        .thank-you {
            margin-top: 15px;
            padding: 15px;
            background: #e8f4fc;
            border-radius: 8px;
            text-align: center;
            color: #3498db;
            display: none;
        }
        
        @media (max-width: 768px) {
            .book-container {
                flex-direction: column;
            }
            
            .book-cover {
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Introduction to Algorithms</h1>
            <div class="authors">CHARLES E. LEISERSON, RONALD L. RIVEST, CLIFFORD STEIN</div>
            <div class="status">Available</div>
        </div>
        
        <div class="book-container">
            <div class="book-cover">
                <img src="https://m.media-amazon.com/images/I/41SNoh5ZhOL._SX442_BO1,204,203,200_.jpg" alt="Introduction to Algorithms Book Cover">
            </div>
            
            <div class="book-details">
                <h2 class="book-title">Book Details</h2>
                
                <div class="book-info">
                    <p>This comprehensive book covers a broad range of algorithms in depth. The book has been widely used as the textbook for algorithms courses at many universities and is commonly cited as a reference for algorithms in published papers.</p>
                    <p>The chapters are written so that they can be read in any order, making it an excellent reference work. It includes two new chapters, on van Emde Boas trees and multithreaded algorithms, and substantial additions to the chapter on recurrence.</p>
                </div>
                
                <div class="rating-section">
                    <h3 class="rating-title">Rate this book (1-5 stars)</h3>
                    <div class="stars">
                        <i class="fas fa-star star" data-value="1"></i>
                        <i class="fas fa-star star" data-value="2"></i>
                        <i class="fas fa-star star" data-value="3"></i>
                        <i class="fas fa-star star" data-value="4"></i>
                        <i class="fas fa-star star" data-value="5"></i>
                    </div>
                    <p>Your rating: <span id="selected-rating">Not rated yet</span></p>
                </div>
                
                <button class="borrow-btn" id="borrow-btn">Request Borrow</button>
                
                <div class="thank-you" id="thank-you">
                    Thank you for your rating! We appreciate your feedback.
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.star');
            const ratingText = document.getElementById('selected-rating');
            const borrowBtn = document.getElementById('borrow-btn');
            const thankYou = document.getElementById('thank-you');
            const status = document.querySelector('.status');
            
            let currentRating = 0;
            let bookReturned = false;
            
            // Add click events to stars
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    if (!bookReturned) return;
                    
                    const value = parseInt(this.getAttribute('data-value'));
                    currentRating = value;
                    
                    // Update star display
                    stars.forEach(s => {
                        const sValue = parseInt(s.getAttribute('data-value'));
                        if (sValue <= value) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                    
                    // Update rating text
                    ratingText.textContent = `${value} star${value !== 1 ? 's' : ''}`;
                });
                
                // Add hover effect
                star.addEventListener('mouseover', function() {
                    if (!bookReturned) return;
                    
                    const value = parseInt(this.getAttribute('data-value'));
                    
                    stars.forEach(s => {
                        const sValue = parseInt(s.getAttribute('data-value'));
                        if (sValue <= value) {
                            s.style.color = '#f1c40f';
                        } else {
                            s.style.color = '#ddd';
                        }
                    });
                });
                
                star.addEventListener('mouseout', function() {
                    if (!bookReturned) return;
                    
                    stars.forEach(s => {
                        const sValue = parseInt(s.getAttribute('data-value'));
                        if (sValue > currentRating) {
                            s.style.color = '#ddd';
                        }
                    });
                });
            });
            
            // Borrow/Return button functionality
            borrowBtn.addEventListener('click', function() {
                if (!bookReturned) {
                    // Simulate borrowing the book
                    this.textContent = 'Return Book';
                    this.classList.add('returned');
                    status.textContent = 'Borrowed';
                    status.style.background = '#e74c3c';
                    bookReturned = true;
                } else {
                    // Book is being returned
                    this.textContent = 'Book Returned - Thank You!';
                    this.disabled = true;
                    status.textContent = 'Available';
                    status.style.background = '#2ecc71';
                    
                    // Show thank you message if rated
                    if (currentRating > 0) {
                        thankYou.style.display = 'block';
                    }
                }
            });
        });
    </script>
</body>
</html>