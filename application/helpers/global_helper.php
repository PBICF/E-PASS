<?php defined('BASEPATH') OR exit('No direct script access allowed');

if(! function_exists('calculate_age')) {
    /**
     * Calculate age from birthdate in DD/MM/YYYY format
     *
     * @param string $birthdate
     * @return int
     */
    function calculate_age(string $birthdate): int
    {
        $birthDate = DateTime::createFromFormat('d/m/Y', $birthdate);
        $today = new DateTime('now');
        $age = $today->diff($birthDate);
        return $age->y;
    }
}

if (! function_exists('custom_404')) {
    /**
     * Custom 404 error handler
     *
     * @return string
     */
    function custom_404()
    {
        $CI =& get_instance();
        $CI->output->set_status_header(404);
        return view('errors.404');
    }
}

if (! function_exists('build_relation_string')) {

    function build_relation_string(array $data): array
    {
        $employee = null;
        $spouse = null;
        $children = [];

        foreach ($data as $row) {
            $row['AGE'] = calculate_age($row['DB']);
            $rel = strtolower($row['RELATION']);

            if ($rel === 'self') {
                $employee = $row;
            } elseif (in_array($rel, ['wife', 'husband'])) {
                $spouse = $row;
            } elseif (in_array($rel, ['son', 'daughter'])) {
                $children[] = $row;
            }
        }

        /* -------- INFER EMPLOYEE GENDER -------- */
        $isFemaleEmployee = ($spouse && strtolower($spouse['RELATION']) === 'husband');

        /* -------- BUILD MAIN PART -------- */
        if ($employee) {
            $title = $isFemaleEmployee ? 'Smt.' : 'Shri.';
            $empName = $title . ' ' . trim(preg_replace('/\s+/', ' ', $employee['NAME']));
            $main = "$empName aged {$employee['AGE']} yrs";
        } else {
            $main = "Employee aged {$spouse['AGE']} yrs";
        }

        /* -------- BUILD EXTRAS -------- */
        $extras = [];

        if ($employee && $spouse) {
            $spouseLabel = ucfirst(strtolower($spouse['RELATION']));
            $extras[] = "with $spouseLabel aged {$spouse['AGE']} yrs";
        }

        if ($children) {
            $grouped = [];

            foreach ($children as $c) {
                $grouped[strtolower($c['RELATION'])][] = $c['AGE'];
            }

            foreach ($grouped as $relation => $ages) {
                sort($ages);
                $count = count($ages);
                $label = ucfirst($relation) . ($count > 1 ? 's' : '');
                $extras[] = "$count $label aged " . implode(' yrs and ', $ages) . " yrs";
            }
        }

        $second = ($extras ? implode(', ', $extras) : '') . ' only.';

        return [$main, $second];
    }
}



if( ! function_exists('redirect_with')) {
    /**
     * Redirect with flashdata
     *
     * @param string $uri       Redirect URI
     * @param array  $flashdata Key-value flash data
     * @param string $method    Redirect method (auto|refresh)
     * @param int    $code      HTTP status code
     */
    function redirect_with($uri, array $flashdata = [], $method = 'auto', $code = NULL)
    {
        $CI =& get_instance();

        if (!empty($flashdata)) {
            foreach ($flashdata as $key => $value) {
                $CI->session->set_flashdata($key, $value);
            }
        }

        redirect($uri, $method, $code);
        exit; // important
    }
}

if (! function_exists('array_change_key_case_recursive')) {
    /**
     * Recursively change array keys case
     *
     * @param array  $array
     * @param string $case  CASE_UPPER | CASE_LOWER
     * @return array
     */
    function array_change_key_case_recursive(array $array, $case = CASE_LOWER)
    {
        $result = [];

        foreach ($array as $key => $value) {
            $key = is_string($key)
                ? ($case === CASE_UPPER ? strtoupper($key) : strtolower($key))
                : $key;

            $result[$key] = is_array($value)
                ? array_change_key_case_recursive($value, $case)
                : $value;
        }

        return $result;
    }
}

if(! function_exists('format_date')) {
    /**
     * Format date from YYYY-MM-DD to DD/MM/YYYY
     *
     * @param string $date
     * @param string $format
     * @return string
     */
    function format_date(string $date, string $format = 'd/m/Y'): string
    {
        $timestamp = strtotime($date);
        return $timestamp ? date($format, $timestamp) : '';
    }
}

if (! function_exists('old_input')) {
    /**
     * Retrieve old input from flashdata
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    function old_input(string $key, $default = null)
    {
        $CI =& get_instance();
        $old = $CI->session->flashdata('old') ?? [];
        return $old[$key] ?? $default;
    }
}

if(! function_exists('prity_print')) {
    /**
     * Pretty print array or object
     *
     * @param mixed $data
     */
    function prity_print(mixed $data)
    {
        echo '<pre>' . htmlspecialchars(print_r($data, true)) . '</pre>';
    }
}

if(! function_exists('is_associative_array')) {
    /**
     * Check if an array is associative
     *
     * @param array $array
     * @return bool
     */
    function is_associative_array(array $array): bool
    {
        if ([] === $array) return false;
        return array_keys($array) !== range(0, count($array) - 1);
    }
}

if(! function_exists('dump')) {
    /**
     * Dump data and terminate script
     * @param mixed ...$data
     */
    function dump()
    {
        $args = func_get_args();
        foreach ($args as $data) {
            prity_print($data);
        }
    }
}

if(! function_exists('dump_and_die')) {
    /**
     * Dump data and terminate script
     *
     * @param mixed $data
     */
    function dump_and_die(mixed $data)
    {
        $args = func_get_args();
        foreach ($args as $data) {
            prity_print($data);
        }
        die();
    }
}

if(! function_exists('flashdata')) {
    /**
     * Get flashdata from session
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    function flashdata(string $key, $default = null)
    {
        $CI =& get_instance();
        $value = $CI->session->flashdata($key);
        return $value !== null ? $value : $default;
    }
}

if(! function_exists('have_error')) {
    /**
     * Check if there is an error for a specific field
     *
     * @param string $field
     * @return bool
     */
    function have_error(string $field): bool
    {
        $CI =& get_instance();
        $errors = $CI->session->flashdata('form_error') ?? [];
        return isset($errors[$field]);
    }
}

if(! function_exists('filter_array')) {
    /**
     * Filter an array to remove null and empty values
     *
     * @param array $array
     * @return array
     */
    function filter_array(array $array): array
    {
        return array_filter($array, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}

if(! function_exists('next_pass_number')) {
    /**
     * 
     */
    function next_pass_number($employee_no)
    {
        $CI =& get_instance();
        $CI->load->model('Trans_model');

        return $CI->Trans_model->next_pass_number($employee_no);
    }
}