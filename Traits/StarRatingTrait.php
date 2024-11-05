<?php

namespace Piffy\Traits;

trait StarRatingTrait {

    // the number of words in the content
    public float $rating = 0.0;
    public int $ratingSum = 0;
    public int $ratingBest = 0;

    /**
     * @return void
     */
    private function loadRating(): void
    {
        $voteFile = USERDATA_DIR . '/star-rating/post_' . $this->getId() . '.json';
        $voteFileData = @file_get_contents($voteFile);

        if ($voteFileData) {
            // var_dump(json_decode($voteFileData));
            $ratingData = json_decode($voteFileData);
            // exit;

            $sum = 0;
            $ratingSum = 0;
            $ratingBest = 0;
            foreach ($ratingData->stars as $key => $val) {
                $sum += ($key + 1) * $val;
                $ratingSum += $val;

                if ($val > 0) {
                    $ratingBest = $key;
                }
            }

            $this->rating = round($sum / $ratingSum, 1);
            $this->ratingSum = $ratingSum;
            $this->ratingBest = $ratingBest;
            // var_dump($this->rating);
        }
    }

}