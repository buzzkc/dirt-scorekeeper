<?php
function calculate_score($bid, $won) {
    if ($bid == 0) {
        return ($won == 0) ? 10 : $won;
    }
    if ($bid == $won) return $bid + 10;
    return $won;
}
?>