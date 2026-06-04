<?php 
// header.php already includes db.php, so the connection $conn is ready to use.
include('header.php'); 
?>

<style>
    :root {
        --enma-bg: #09090b;
        --enma-card: #121214;
        --enma-border: #27272a;
        --enma-yellow: #facc15;
        --enma-text-muted: #a1a1aa;
    }

    body { background-color: var(--enma-bg); color: white; }

    /* --- HERO CAROUSEL STYLING --- */
    .hero-slide {
        position: relative;
        height: 650px;
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
    }

    .hero-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to right, #000 15%, transparent 100%),
                    linear-gradient(to top, var(--enma-bg) 5%, transparent 30%);
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 700px;
    }

    .movie-subtitle {
        color: var(--enma-text-muted);
        font-weight: 700;
        letter-spacing: 3px;
        text-transform: uppercase;
        font-size: 0.9rem;
    }

    .movie-title {
        font-size: 5.5rem;
        font-weight: 800;
        color: var(--enma-yellow);
        line-height: 0.9;
        margin: 15px 0;
        text-transform: uppercase;
    }

    .btn-enma-lg {
        background: var(--enma-yellow);
        color: #000;
        font-weight: 800;
        padding: 15px 40px;
        border-radius: 10px;
        transition: 0.3s;
        letter-spacing: 1px;
    }

    .btn-enma-lg:hover {
        background: #fff;
        transform: translateY(-3px);
    }

    /* --- MOVIE CARD STYLING --- */
    .movie-card {
        transition: 0.3s;
        border-radius: 16px;
        overflow: hidden;
    }

    .movie-card:hover {
        transform: translateY(-10px);
    }

    .poster-wrapper img {
        transition: 0.5s;
        width: 100%;
        height: 420px;
        object-fit: cover;
    }

    .movie-card:hover img {
        filter: brightness(1.2);
    }

    .gsc-indicators [data-bs-target] {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: var(--enma-text-muted);
    }

    .gsc-indicators .active {
        background-color: var(--enma-yellow);
        width: 30px;
        border-radius: 10px;
    }
</style>

<!-- --- DYNAMIC HERO CAROUSEL --- -->
<div id="enmaHeroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="6000">
    <div class="carousel-indicators gsc-indicators">
        <?php
        $count_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM movies");
        $total_movies = mysqli_fetch_assoc($count_res)['total'];
        for ($i = 0; $i < $total_movies; $i++) {
            $active_class = ($i === 0) ? 'active' : '';
            echo '<button type="button" data-bs-target="#enmaHeroCarousel" data-bs-slide-to="'.$i.'" class="'.$active_class.'"></button>';
        }
        ?>
    </div>

    <div class="carousel-inner">
        <?php
        $result = mysqli_query($conn, "SELECT * FROM movies ORDER BY id DESC");
        $first = true;
        if (mysqli_num_rows($result) > 0):
            while($row = mysqli_fetch_assoc($result)):
        ?>
            <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                <!-- Corrected path to img/ folder -->
                <div class="hero-slide" style="background-image: url('img/<?php echo $row['poster_image']; ?>');">
                    <div class="hero-overlay"></div>
                    <div class="container">
                        <div class="hero-content">
                            <p class="movie-subtitle mb-0"><?php echo !empty($row['subtitle']) ? htmlspecialchars($row['subtitle']) : 'NOW SHOWING'; ?></p>
                            <h1 class="movie-title"><?php echo htmlspecialchars($row['title']); ?></h1>
                            <div class="d-flex align-items-center gap-4 mt-4">
                                <a href="booking.php?id=<?php echo $row['id']; ?>" class="btn btn-enma-lg text-decoration-none">BUY NOW</a>
                                <div class="text-white fw-bold">
                                    <i class="bi bi-star-fill text-warning me-1"></i> <?php echo $row['rating']; ?> RATING
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php 
            $first = false;
            endwhile; 
        else: ?>
            <div class="carousel-item active">
                <div class="hero-slide" style="background:#121212;">
                    <div class="container text-center"><h1>Welcome to ENMA</h1><p>Check back soon for latest movies!</p></div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#enmaHeroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#enmaHeroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- --- NOW SHOWING GRID --- -->
<div class="container py-5 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="fw-bold display-6">Now Showing</h2>
        <a href="#" class="text-warning text-decoration-none fw-bold small tracking-widest">VIEW ALL <i class="bi bi-chevron-right"></i></a>
    </div>

    <div class="row g-4">
        <?php
        $movie_query = mysqli_query($conn, "SELECT * FROM movies ORDER BY id DESC");
        while($row = mysqli_fetch_assoc($movie_query)): 
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <a href="booking.php?id=<?php echo $row['id']; ?>" class="text-decoration-none text-white">
                <div class="movie-card">
                    <div class="poster-wrapper mb-3">
                        <img src="img/<?php echo $row['poster_image']; ?>" 
                             class="img-fluid rounded-4 shadow-lg" 
                             alt="<?php echo htmlspecialchars($row['title']); ?>"
                             onerror="this.src='https://via.placeholder.com/300x450?text=No+Poster'">
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo htmlspecialchars($row['title']); ?></h5>
                    <p class="text-secondary small mb-0"><?php echo htmlspecialchars($row['genre']); ?></p>
                </div>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include('footer.php'); ?>