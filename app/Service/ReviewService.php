<?php

namespace App\Service;

use App\Models\Review;

class ReviewService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Returns a list of Review objects that have open set to true.
     */
    public function addReview(array $data,  $appointment)
    {
        $review = new Review();
        $review->appointment_id = $appointment->id;
        $review->comment = $data['review'];
        $review->pdf = $data['pdf'];
        $review->save();

      // update the status of the appointment after review added
        $appointment->status = 'reviewed';
        $appointment->save();
        // if($review){
        //     return ;
        // }

        // return "Review ";
    }
}
