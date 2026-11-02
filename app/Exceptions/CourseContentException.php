<?php

namespace App\Exceptions;

use RuntimeException;

/** A fixed, author-safe import error without source bodies or request details. */
class CourseContentException extends RuntimeException {}
