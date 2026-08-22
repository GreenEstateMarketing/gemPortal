<?php

namespace App\models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class table_properties_check_lists extends Model
{
    use HasFactory;
    protected $table = 'table_properties_check_lists';
    protected $fillable=['completion_document','allotment_document','possession_document','property_id','document_checklist','is_verify'];
}
