<?php
require_once __DIR__ . '../config/db.php';
require_once __DIR__ . '../includes/functions.php';
include __DIR__ . '../includes/header.php';

// Function to get average rating for a book
function getAverageRating($conn, $book_id) {
    $stmt = $conn->prepare("SELECT AVG(rating_value) as avg_rating, COUNT(*) as total_ratings FROM ratings WHERE book_id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to get reviews for a book
function getBookReviews($conn, $book_id) {
    $stmt = $conn->prepare("SELECT r.rating_value, r.review_comment, r.created_at, r.student_no 
                           FROM ratings r 
                           WHERE r.book_id = ? AND r.review_comment IS NOT NULL AND r.review_comment != ''
                           ORDER BY r.created_at DESC");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

$kw = isset($_GET['q']) ? sanitize($conn, $_GET['q']) : '';
if ($kw !== '') {
    $like = "%$kw%";
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ? OR category LIKE ? ORDER BY title ASC");
    $stmt->bind_param("ssss", $like, $like, $like, $like);
} else {
    $stmt = $conn->prepare("SELECT * FROM books ORDER BY title ASC");
}
$stmt->execute();
$result = $stmt->get_result();
?>
<section class="py-2">
  <div class="row mb-3">
    <div class="col-lg-8 mx-auto">
      <form class="d-flex gap-2" method="get">
        <input class="form-control search-pill" type="text" name="q" placeholder="Search by title, author, ISBN, or category" value="<?php echo htmlspecialchars($kw); ?>">
        <button class="btn btn-primary px-4">Search</button>
        <a href="/library-inventory-frontend/index.php" class="btn btn-outline-secondary">Reset</a>
      </form>
      <p class="small-muted mt-2">Tip: You can request a borrow directly from each book card.</p>
    </div>
  </div>

  <link rel="stylesheet" href="/library-inventory-frontend/public/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .rating-stars {
        color: #ddd;
        font-size: 0.9rem;
    }
    .rating-stars .filled {
        color: #f1c40f;
    }
    .rating-value {
        font-size: 1.1rem;
        font-weight: bold;
        color: #333;
    }
    .rating-count {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .book-cover {
        cursor: pointer;
        transition: opacity 0.2s;
    }
    .book-cover:hover {
        opacity: 0.85;
    }
    .review-card {
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
        background-color: #f8f9fa;
    }
    .review-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    .review-student {
        font-weight: 500;
    }
    .review-date {
        color: #6c757d;
        font-size: 0.875rem;
    }
    .review-comment {
        margin-bottom: 0.5rem;
    }
    .review-rating {
        color: #f1c40f;
    }
    .no-reviews {
        color: #6c757d;
        font-style: italic;
        text-align: center;
        padding: 1.5rem;
    }
    .rating-display {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .rating-large {
        font-size: 1.4rem;
        font-weight: bold;
    }
    .rating-breakdown {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-top: 10px;
    }
    .rating-bar {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .rating-bar-label {
        width: 20px;
        text-align: center;
        font-size: 0.9rem;
    }
    .rating-bar-progress {
        flex-grow: 1;
        height: 8px;
        background-color: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }
    .rating-bar-fill {
        height: 100%;
        background-color: #f1c40f;
    }
    .rating-bar-count {
        width: 30px;
        text-align: right;
        font-size: 0.8rem;
        color: #6c757d;
    }
  </style>
  
  <div class="row g-3">
    <?php while ($b = $result->fetch_assoc()): 
        $rating_data = getAverageRating($conn, $b['id']);
        $avg_rating = $rating_data['avg_rating'] ? round($rating_data['avg_rating'], 1) : 0;
        $total_ratings = $rating_data['total_ratings'] ? $rating_data['total_ratings'] : 0;
        
        // Get rating distribution for this book
        $rating_distribution = array(0, 0, 0, 0, 0);
        if ($total_ratings > 0) {
            $stmt_dist = $conn->prepare("SELECT rating_value, COUNT(*) as count FROM ratings WHERE book_id = ? GROUP BY rating_value");
            $stmt_dist->bind_param("i", $b['id']);
            $stmt_dist->execute();
            $result_dist = $stmt_dist->get_result();
            while ($row = $result_dist->fetch_assoc()) {
                if ($row['rating_value'] >= 1 && $row['rating_value'] <= 5) {
                    $rating_distribution[5 - $row['rating_value']] = $row['count'];
                }
            }
        }
    ?>
    <div class="col-12 col-sm-6 col-lg-4">
      <div class="card card-hover h-100" >
        <img class="book-cover card-img-top" 
             src="<?php echo !empty($b['image']) 
                ? '/library-inventory-frontend/' . htmlspecialchars($b['image']) 
                : 'https://via.placeholder.com/200x250?text=No+Cover'; ?>" 
             alt="Cover"
             style="height: 200px; object-fit: cover;"
             data-bs-toggle="modal" 
             data-bs-target="#bookDetailModal"
             data-book-id="<?php echo (int)$b['id']; ?>"
             data-book-title="<?php echo htmlspecialchars($b['title']); ?>"
             data-book-author="<?php echo htmlspecialchars($b['author']); ?>"
             data-book-isbn="<?php echo htmlspecialchars($b['isbn']); ?>"
             data-book-category="<?php echo htmlspecialchars($b['category']); ?>"
             data-book-copies-available="<?php echo (int)$b['copies_available']; ?>"
             data-book-copies-total="<?php echo (int)$b['copies_total']; ?>"
             data-book-status="<?php echo htmlspecialchars($b['status']); ?>"
             data-book-description="<?php echo !empty($b['description']) ? htmlspecialchars($b['description']) : 'No description available.'; ?>"
             data-book-image="<?php echo !empty($b['image']) ? '/library-inventory-frontend/' . htmlspecialchars($b['image']) : 'https://via.placeholder.com/200x250?text=No+Cover'; ?>"
             data-book-rating-avg="<?php echo $avg_rating; ?>"
             data-book-rating-count="<?php echo $total_ratings; ?>"
             data-book-rating-distribution="<?php echo htmlspecialchars(json_encode($rating_distribution)); ?>">

        <div class="card-body">
          <h5 class="card-title mb-1"><?php echo htmlspecialchars($b['title']); ?></h5>
          <div class="small-muted mb-2"><?php echo htmlspecialchars($b['author']); ?></div>
          
          <!-- Rating Display -->
          <div class="mb-2">
            <div class="rating-display">
              <span class="rating-value"><?php echo $avg_rating > 0 ? $avg_rating : '0.0'; ?></span>
              <div class="rating-stars">
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= floor($avg_rating)) {
                        echo '<i class="fas fa-star filled"></i>';
                    } elseif ($i == ceil($avg_rating) && fmod($avg_rating, 1) >= 0.5) {
                        echo '<i class="fas fa-star-half-alt filled"></i>';
                    } else {
                        echo '<i class="far fa-star"></i>';
                    }
                }
                ?>
              </div>
            </div>
            <div class="rating-count">
              <?php echo $total_ratings > 0 ? number_format($total_ratings) . ' ratings' : 'No ratings yet'; ?>
              <!-- Rating Breakdown (on card, always visible like the picture) -->
<div class="rating-breakdown mt-2">
  <?php
    $total = array_sum($rating_distribution);
    for ($i = 5; $i >= 1; $i--) {
        $count = $rating_distribution[5 - $i];
        $percentage = $total > 0 ? ($count / $total) * 100 : 0;
        echo '
        <div class="rating-bar">
            <div class="rating-bar-label">'.$i.'</div>
            <div class="rating-bar-progress">
                <div class="rating-bar-fill" style="width: '.$percentage.'%;"></div>
            </div>
            <div class="rating-bar-count">'.$count.'</div>
        </div>';
    }
  ?>
</div>

            </div>
          </div>
          
          <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="badge rounded-pill text-bg-<?php echo ($b['copies_available']>0 && $b['status']=='available')?'success':'secondary'; ?>">
              <?php echo ($b['copies_available']>0 && $b['status']=='available')?'Available':'Unavailable'; ?>
            </span>
            <span class="small-muted">Copies: <?php echo (int)$b['copies_available']; ?> / <?php echo (int)$b['copies_total']; ?></span>
          </div>
        </div>
        <div class="card-footer bg-white border-0 pt-0 pb-3 px-3">
          <div class="d-grid">
           
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</section>















































<!-- Book Detail Modal with Rating System and Reviews -->
<div class="modal fade" id="bookDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bookDetailModalTitle">Book Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4">
            <img id="modalBookImage" src="" class="img-fluid rounded" alt="Book Cover">
          </div>
          <div class="col-md-8">
            <h3 id="modalBookTitle"></h3>
            <p class="text-muted" id="modalBookAuthor"></p>
            
            <!-- Rating Display in Modal -->
            <div class="mb-3" id="modalBookRating">
                <div class="rating-display">
                  <span class="rating-large" id="modalBookRatingValue"></span>
                  <div class="rating-stars" style="font-size: 1.2rem;">
                    <!-- Stars will be populated by JavaScript -->
                  </div>
                </div>
                <div class="rating-count" id="modalBookRatingCount"></div>
                
                <!-- Rating Breakdown -->
                <div class="rating-breakdown mt-3" id="ratingBreakdown">
                  <!-- Rating bars will be populated by JavaScript -->
                </div>
            </div>
            
            <div class="mb-3">
              <span class="badge bg-info" id="modalBookStatus"></span>
              <span class="ms-2 text-muted" id="modalBookCopies"></span>
            </div>
            
            <div class="book-details mb-3">
              <p><strong>Year Publish:</strong> <span id="modalBookYear"></span></p>
              <p><strong>ISBN:</strong> <span id="modalBookIsbn"></span></p>
              <p><strong>Category:</strong> <span id="modalBookCategory"></span></p>
            </div>
            
            <div class="book-description mb-3">
              <h6>Description</h6>
              <p id="modalBookDescription"></p>
            </div>
            
            <!-- Rating Section -->
            <div class="rating-section border-top pt-3">
              <h6>Rate this Book</h6>
              <div class="rating-stars mb-2" style="font-size: 1.5rem;">
                <i class="fas fa-star" data-rating="1"></i>
                <i class="fas fa-star" data-rating="2"></i>
                <i class="fas fa-star" data-rating="3"></i>
                <i class="fas fa-star" data-rating="4"></i>
                <i class="fas fa-star" data-rating="5"></i>
              </div>
              <p>Your rating: <span id="selected-rating">Not rated yet</span></p>
              
              <form id="ratingForm" method="post" action="../library-inventory-frontend/public/submit_rating.php">
                <input type="hidden" name="book_id" id="ratingBookId">
                <input type="hidden" name="rating_value" id="ratingValue">
                <input type="hidden" name="redirect_to" value="index.php">
                
                <!-- Student Number Field -->
                <div class="mb-3">
                  <label for="ratingStudentNo" class="form-label">Student Number *</label>
                  <input type="text" class="form-control" id="ratingStudentNo" name="student_no" required>
                </div>
                
                <div class="mb-3">
                  <label for="reviewComment" class="form-label">Review (optional)</label>
                  <textarea class="form-control" id="reviewComment" name="review_comment" rows="2"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-sm">Submit Rating</button>
              </form>
            </div>
            
            <!-- Reviews Section (Read-only) -->
            <div class="reviews-section border-top pt-3 mt-3">
              <h6>User Reviews</h6>
              <div id="reviewsContainer">
                <!-- Reviews will be loaded here via AJAX -->
                <div class="no-reviews">Loading reviews...</div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#borrowModal" id="borrowFromDetailBtn">Borrow This Book</button>
      </div>
    </div>
  </div>
</div>

<!-- Borrow Modal (existing) -->
<div class="modal fade" id="borrowModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="../library-inventory-frontend/public/request_borrow.php">
        <div class="modal-header">
          <h5 class="modal-title">Request Borrow</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <!-- Student Number -->
          <div class="mb-3">
            <label for="student_no" class="form-label">Student Number</label>
            <input type="text" class="form-control" id="student_no" name="student_no" required>
          </div>

          <!-- Hidden Book ID -->
          <input type="hidden" id="book_id" name="book_id">

          <!-- Quantity -->
          <div class="mb-3">
            <label for="qty" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="qty" name="qty" value="1" min="1" required>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Submit Request</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>













































  // When modal opens, fill hidden input with book id
  var borrowModal = document.getElementById('borrowModal');
  borrowModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget; // Button that triggered the modal
    var bookId = button.getAttribute('data-id');
    document.getElementById('book_id').value = bookId;
  });

  // Book Detail Modal functionality
  var bookDetailModal = document.getElementById('bookDetailModal');
  bookDetailModal.addEventListener('show.bs.modal', function (event) {
    var cover = event.relatedTarget;
    
    // Populate modal with book data
    var bookId = cover.getAttribute('data-book-id');
    document.getElementById('modalBookTitle').textContent = cover.getAttribute('data-book-title');
    document.getElementById('modalBookAuthor').textContent = cover.getAttribute('data-book-author');
    document.getElementById('modalBookYear').textContent = cover.getAttribute('data-book-year_publish');
    document.getElementById('modalBookIsbn').textContent = cover.getAttribute('data-book-isbn');
    document.getElementById('modalBookCategory').textContent = cover.getAttribute('data-book-category');
    document.getElementById('modalBookDescription').textContent = cover.getAttribute('data-book-description');
    document.getElementById('modalBookImage').src = cover.getAttribute('data-book-image');
    
    // Set status and copies
    var status = cover.getAttribute('data-book-status');
    var copiesAvailable = cover.getAttribute('data-book-copies-available');
    var copiesTotal = cover.getAttribute('data-book-copies-total');
    
    document.getElementById('modalBookStatus').textContent = 
      (copiesAvailable > 0 && status == 'available') ? 'Available' : 'Unavailable';
    document.getElementById('modalBookStatus').className = 
      (copiesAvailable > 0 && status == 'available') ? 'badge bg-success' : 'badge bg-secondary';
    document.getElementById('modalBookCopies').textContent = 
      'Copies: ' + copiesAvailable + ' / ' + copiesTotal;
    
    // Set book ID for rating form
    document.getElementById('ratingBookId').value = bookId;
    
    // Set up borrow button in modal footer
    var borrowBtn = document.getElementById('borrowFromDetailBtn');
    borrowBtn.setAttribute('data-id', bookId);
    borrowBtn.setAttribute('data-title', cover.getAttribute('data-book-title'));
    
    if (copiesAvailable <= 0 || status != 'available') {
      borrowBtn.disabled = true;
      borrowBtn.className = 'btn btn-secondary';
      borrowBtn.textContent = 'Not Available for Borrowing';
    } else {
      borrowBtn.disabled = false;
      borrowBtn.className = 'btn btn-primary';
      borrowBtn.textContent = 'Borrow This Book';
    }
    
    // Display rating information
    var avgRating = parseFloat(cover.getAttribute('data-book-rating-avg'));
    var ratingCount = parseInt(cover.getAttribute('data-book-rating-count'));
    var ratingDistribution = JSON.parse(cover.getAttribute('data-book-rating-distribution'));
    
    var ratingStarsContainer = document.querySelector('#modalBookRating .rating-stars');
    ratingStarsContainer.innerHTML = '';
    
    if (avgRating > 0) {
        for (var i = 1; i <= 5; i++) {
            var star = document.createElement('i');
            if (i <= Math.floor(avgRating)) {
                star.className = 'fas fa-star filled';
            } else if (i == Math.ceil(avgRating) && (avgRating % 1) >= 0.5) {
                star.className = 'fas fa-star-half-alt filled';
            } else {
                star.className = 'far fa-star';
            }
            ratingStarsContainer.appendChild(star);
        }
        
        document.getElementById('modalBookRatingValue').textContent = avgRating;
        document.getElementById('modalBookRatingCount').textContent = ratingCount + ' ratings';
        
        // Display rating breakdown
        var breakdownContainer = document.getElementById('ratingBreakdown');
        breakdownContainer.innerHTML = '';
        
        var totalRatings = ratingDistribution.reduce((a, b) => a + b, 0);
        
        for (var i = 5; i >= 1; i--) {
            var count = ratingDistribution[5 - i];
            var percentage = totalRatings > 0 ? (count / totalRatings) * 100 : 0;
            
            var barHtml = `
                <div class="rating-bar">
                    <div class="rating-bar-label">${i}</div>
                    <div class="rating-bar-progress">
                        <div class="rating-bar-fill" style="width: ${percentage}%"></div>
                    </div>
                    <div class="rating-bar-count">${count}</div>
                </div>
            `;
            breakdownContainer.innerHTML += barHtml;
        }
    } else {
        ratingStarsContainer.innerHTML = 'No ratings yet';
        document.getElementById('modalBookRatingValue').textContent = '0.0';
        document.getElementById('modalBookRatingCount').textContent = 'No ratings yet';
        document.getElementById('ratingBreakdown').innerHTML = '<div class="no-reviews">No rating data available</div>';
    }
    
    // Reset rating form
    document.getElementById('selected-rating').textContent = 'Not rated yet';
    document.getElementById('ratingValue').value = '';
    document.getElementById('ratingStudentNo').value = '';
    document.getElementById('reviewComment').value = '';
    document.querySelectorAll('.rating-stars .fas').forEach(star => {
        star.classList.remove('text-warning');
    });
    
    // Load reviews via AJAX
    loadReviews(bookId);
  });

  // Function to load reviews via AJAX
  function loadReviews(bookId) {
    var reviewsContainer = document.getElementById('reviewsContainer');
    reviewsContainer.innerHTML = '<div class="no-reviews">Loading reviews...</div>';
    
    // Create AJAX request
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '../library-inventory-frontend/public/get_reviews.php?book_id=' + bookId, true);
    xhr.onload = function() {
      if (this.status == 200) {
        try {
          var reviews = JSON.parse(this.responseText);
          displayReviews(reviews);
        } catch (e) {
          reviewsContainer.innerHTML = '<div class="no-reviews">Error loading reviews</div>';
        }
      } else {
        reviewsContainer.innerHTML = '<div class="no-reviews">Error loading reviews</div>';
      }
    };
    xhr.onerror = function() {
      reviewsContainer.innerHTML = '<div class="no-reviews">Error loading reviews</div>';
    };
    xhr.send();
  }

  // Function to display reviews
  function displayReviews(reviews) {
    var reviewsContainer = document.getElementById('reviewsContainer');
    
    if (reviews.length === 0) {
      reviewsContainer.innerHTML = '<div class="no-reviews">No reviews yet</div>';
      return;
    }
    
    var html = '';
    reviews.forEach(function(review) {
      // Format date
      var reviewDate = new Date(review.created_at);
      var formattedDate = reviewDate.toLocaleDateString();
      
      // Create stars for rating
      var starsHtml = '';
      for (var i = 1; i <= 5; i++) {
        if (i <= review.rating_value) {
          starsHtml += '<i class="fas fa-star review-rating"></i>';
        } else {
          starsHtml += '<i class="far fa-star"></i>';
        }
      }
      
      html += '<div class="review-card">' +
                '<div class="review-header">' +
                  '<span class="review-student">' + review.student_no + '</span>' +
                  '<span class="review-date">' + formattedDate + '</span>' +
                '</div>' +
                '<div class="review-comment">' + review.review_comment + '</div>' +
                '<div class="review-rating">' + starsHtml + '</div>' +
              '</div>';
    });
    
    reviewsContainer.innerHTML = html;
  }

  // Rating stars functionality
  document.querySelectorAll('.rating-stars .fas').forEach(star => {
    star.addEventListener('click', function() {
        const rating = this.getAttribute('data-rating');
        document.getElementById('ratingValue').value = rating;
        document.getElementById('selected-rating').textContent = rating + ' star' + (rating > 1 ? 's' : '');
        
        // Update star display
        document.querySelectorAll('.rating-stars .fas').forEach(s => {
            if (s.getAttribute('data-rating') <= rating) {
                s.classList.add('text-warning');
            } else {
                s.classList.remove('text-warning');
            }
        });
    });
    
    // Hover effect for stars
    star.addEventListener('mouseover', function() {
        const rating = this.getAttribute('data-rating');
        document.querySelectorAll('.rating-stars .fas').forEach(s => {
            if (s.getAttribute('data-rating') <= rating) {
                s.classList.add('text-warning');
            }
        });
    });
    
    star.addEventListener('mouseout', function() {
        const currentRating = document.getElementById('ratingValue').value;
        document.querySelectorAll('.rating-stars .fas').forEach(s => {
            if (!currentRating || s.getAttribute('data-rating') > currentRating) {
                s.classList.remove('text-warning');
            }
        });
    });
  });
</script>

<?php include __DIR__ . '../includes/footer.php'; ?>