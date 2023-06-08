<?php

    function filterReviews(){

        //Load reviews from JSON file
        $file = file_get_contents("reviews.json");
        $reviews = json_decode($file, false);

        if(isset($_POST["Filter"]) == false) return $reviews;

        //Extract filter parameters
        $orderRating = $_POST["OrderRating"];
        $minimumRating = (int)$_POST["MinimumRating"];
        $orderDate = $_POST["OrderDate"];
        $prioritizeText = $_POST["PrioritizeText"] == "yes";

        //Filter reviews by minimum rating
        $filtered = array_filter($reviews, function($review) use ($minimumRating){
            return (int)$review->rating >= $minimumRating;
        });
        

        //Sort reviews
        usort($filtered, function($a, $b) use ($orderRating, $orderDate, $prioritizeText){

            // Compare by text (if prioritized)
            if ($prioritizeText) {
                if (!empty($a->reviewText) && empty($b->reviewText)) {
                    return -1;
                } elseif (empty($a->reviewText) && !empty($b->reviewText)) {
                    return 1;
                }
            }

            // Compare by rating
            if ($a->rating != $b->rating) {
                if ($orderRating == "desc") {
                    return $b->rating <=> $a->rating;
                } else {
                    return $a->rating <=> $b->rating;
                }
            }

            // Compare by date
            if ($a->reviewCreatedOnTime != $b->reviewCreatedOnTime) {
                if ($orderDate == "asc") {
                    return $b->reviewCreatedOnTime <=> $a->reviewCreatedOnTime;
                } else {
                    return $a->reviewCreatedOnTime <=> $b->reviewCreatedOnTime;
                }
            }

            return 0; // Reviews are equal

        });

        return $filtered;

    }

?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reviews</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    </head>

    <body>
        
        <div class="container my-4">

            <h1 class="fw-light text-center text-primary mb-4">Filter Reviews</h1>

            <div class="card card-body mb-4">

                <form action="index.php" method="POST">

                    <div class="row">

                        <div class="col-md-3">
                            <label for="minimum-rating" class="form-label">Minimum rating</label>
                            <select name="MinimumRating" id="minimum-rating" class="form-select">
                                <option value="1" <?php echo isset($_POST["MinimumRating"]) && $_POST["MinimumRating"] == "1" ? "selected" : ""; ?> >1</option>
                                <option value="2" <?php echo isset($_POST["MinimumRating"]) && $_POST["MinimumRating"] == "2" ? "selected" : ""; ?> >2</option>
                                <option value="3" <?php echo isset($_POST["MinimumRating"]) && $_POST["MinimumRating"] == "3" ? "selected" : ""; ?> >3</option>
                                <option value="4" <?php echo isset($_POST["MinimumRating"]) && $_POST["MinimumRating"] == "4" ? "selected" : ""; ?> >4</option>
                                <option value="5" <?php echo isset($_POST["MinimumRating"]) && $_POST["MinimumRating"] == "5" ? "selected" : ""; ?> >5</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="order-rating" class="form-label">Order by rating</label>
                            <select name="OrderRating" id="order-rating" class="form-select">
                                <option value="desc" <?php echo isset($_POST["OrderRating"]) && $_POST["OrderRating"] == "desc" ? "selected" : ""; ?>>Highest First</option>
                                <option value="asc" <?php echo isset($_POST["OrderRating"]) && $_POST["OrderRating"] == "asc" ? "selected" : ""; ?>>Lowest First</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="order-date" class="form-label">Order by date</label>
                            <select name="OrderDate" id="order-date" class="form-select">
                                <option value="desc" <?php echo isset($_POST["OrderDate"]) && $_POST["OrderDate"] == "desc" ? "selected" : ""; ?>>Oldest First</option>
                                <option value="asc" <?php echo isset($_POST["OrderDate"]) && $_POST["OrderDate"] == "asc" ? "selected" : ""; ?>>Newest First</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-center pt-md-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="PrioritizeText" value="no">
                                <input class="form-check-input" type="checkbox" name="PrioritizeText" value="yes" id="filter-prioritize-text" <?php echo isset($_POST["PrioritizeText"]) && $_POST["PrioritizeText"] == "yes" ? "checked" : ""; ?>>
                                <label class="form-check-label" for="filter-prioritize-text">Prioritize by text</label>
                            </div>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" name="Filter" value="Filter" class="btn btn-primary">Filter</button>
                        </div>

                    </div>

                </form>
                
            </div>
                
            <div class="card table-responsive">
                <table class="table mb-0">
                    <thead>
                        <th>Review Text</th>
                        <th>Rating</th>
                        <th>Date</th>
                    </thead>
                    <tbody>
                        <?php foreach(filterReviews() as $review): ?>
                            <tr>
                                <td><?php echo htmlentities($review->reviewText); ?></td>
                                <td><?php echo htmlentities($review->rating); ?></td>
                                <td><?php echo htmlentities($review->reviewCreatedOn); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </body>

</html>