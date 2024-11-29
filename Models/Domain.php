<?php

namespace Paymenter\Extensions\Others\DomainName\Models;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $table = 'domains';

    protected $fillable = [
        'user_id',
        'service_id',
        'register_name',
        'domain',
        'ns1',
        'ns2',
        'ns3',
        'ns4',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
