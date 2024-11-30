<?php

namespace App\Enums;

enum MessageType: string
{
    case TEXT = 'text';
    case VOCAL = 'vocal';
    case IMAGE = 'image';
}
