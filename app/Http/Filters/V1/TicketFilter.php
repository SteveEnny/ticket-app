<?php

namespace App\Http\Filter\V1;

class TicketFilter extends QueryFilter{

    // 1. bring in the method from the queryfilter
    // 2. if any method in this class is the key writing in the appl method call that method..

    public function status($value) {
        return $this->builder->whereIn('status', explode(',' , $value));
    }

    public function include($value) {
        return $this->builder->with($value);
    }
    
    public function title($value) {
        $likeStr = str_replace('*', '%', $value);
        return $this->builder->where('title', 'like', $likeStr);
    }

    public function createdAt($value) {
        $dates = explode(',', $value);
        if(count($dates) > 1) {
            return $this->builder->whereBetween('created_at', $dates);
        }

        return $this->builder->whereDate('created_at', $value);
    }

    public function updatedAt($value) {
        $dates = explode(', ', $value);
        if(count($dates) > 1) {
            return $this->builder->whereBetween('updated_at', $dates);
        }

        return $this->builder->whereDate('updated_at', $value);
    }
}