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

    function build_relation_string(array $data, array $employee): array
    {
        $employeeMember = null;
        $spouseMembers = [];
        $otherMembers = [];

        $format_name = static function (?string $name): string {
            $clean = trim((string) preg_replace('/\s+/', ' ', (string) $name));
            return strtoupper($clean);
        };

        $resolve_age = static function (array $row, ?array $employee = null): string {
            $rawAge = trim((string) ($row['AGE'] ?? ''));
            if ($rawAge !== '' && ctype_digit($rawAge) && (int) $rawAge > 0) {
                return $rawAge;
            }

            $db = trim((string) ($row['DB'] ?? ''));
            if ($db !== '') {
                $age = calculate_age($db);
                if ($age > 0) {
                    return (string) $age;
                }
            }

            $rel = strtolower(trim((string) ($row['RELATION'] ?? '')));
            if ($rel === 'self' && ! empty($employee['DTBIRTH'])) {
                $empAge = calculate_age((string) $employee['DTBIRTH']);
                if ($empAge > 0) {
                    return (string) $empAge;
                }
            }

            return '';
        };

        $format_relation = static function (?string $relation): string {
            $clean = trim((string) preg_replace('/\s+/', ' ', (string) $relation));
            if ($clean === '') {
                return '';
            }

            // Title-case alphabetic words, but keep separators like / - and digits.
            return preg_replace_callback('/[A-Za-z]+/', static function (array $m) {
                $word = strtolower($m[0]);
                if ($word === 'wd') {
                    return 'Wd';
                }
                if ($word === 'pwd') {
                    return strtoupper($word);
                }
                return ucfirst($word);
            }, $clean);
        };

        $pluralize_relation = static function (string $relation): string {
            if ($relation === '') {
                return $relation;
            }

            if (preg_match('/\bDaughter\b/i', $relation)) {
                return preg_replace('/\bDaughter\b/i', 'Daughters', $relation);
            }

            if (preg_match('/\bSon\b/i', $relation)) {
                return preg_replace('/\bSon\b/i', 'Sons', $relation);
            }

            if (preg_match('/s$/i', $relation)) {
                return $relation;
            }

            return $relation . 's';
        };

        $is_spouse_relation = static function (string $relation): bool {
            $r = strtolower(trim($relation));
            return (bool) preg_match('/\b(wife|husband)\b/', $r);
        };

        foreach ($data as &$row) {
            $row['AGE'] = $resolve_age($row, $employee);
            $rel = strtolower(trim((string) ($row['RELATION'] ?? '')));

            if ($rel === 'self') {
                $employeeMember = $row;
            } elseif ($is_spouse_relation($rel)) {
                $spouseMembers[] = $row;
            } elseif (in_array($rel, ['son', 'daughter'])) {
                $otherMembers[] = $row;
            } else {
                $otherMembers[] = $row;
            }
        }
        unset($row);

        // CASE 1: SELF exists (normal mixed family list)
        if ($employeeMember) {
            $isFemaleEmployee = false;
            if (! empty($spouseMembers)) {
                $spouseRel = strtolower(trim((string) ($spouseMembers[0]['RELATION'] ?? '')));
                $isFemaleEmployee = ($spouseRel === 'husband');
            } elseif (isset($employee['SHRI'])) {
                $isFemaleEmployee = ((string) $employee['SHRI'] !== '1');
            }

            $title = $isFemaleEmployee ? 'Smt.' : 'Shri.';
            $empName = $format_name($employeeMember['NAME'] ?? '');
            $main = "{$title} {$empName} aged {$employeeMember['AGE']} yrs";

            $depend1Extras = [];
            if (! empty($spouseMembers)) {
                $spouse = $spouseMembers[0];
                $spouseLabel = $format_relation($spouse['RELATION'] ?? '');
                $depend1Extras[] = "with {$spouseLabel} aged {$spouse['AGE']} yrs";
            }

            $depend2Extras = [];
            if (! empty($otherMembers)) {
                $grouped = [];
                foreach ($otherMembers as $member) {
                    $relationKey = strtolower(trim((string) ($member['RELATION'] ?? '')));
                    $relationLabel = $format_relation($member['RELATION'] ?? '');
                    if (! isset($grouped[$relationKey])) {
                        $grouped[$relationKey] = ['label' => $relationLabel, 'ages' => []];
                    }
                    $grouped[$relationKey]['ages'][] = $member['AGE'];
                }

                foreach ($grouped as $group) {
                    $ages = array_values(array_filter($group['ages'], static fn($a) => $a !== ''));
                    $count = count($ages);
                    if ($count === 0) {
                        continue;
                    }

                    $relationLabel = $group['label'];
                    if ($count > 1) {
                        $relationLabel = $pluralize_relation($relationLabel);
                        $depend2Extras[] = "{$count} {$relationLabel} aged " . implode(' yrs and ', $ages) . " yrs";
                    } else {
                        $depend2Extras[] = "{$relationLabel} aged {$ages[0]} yrs";
                    }
                }
            }

            $depend1 = $main;
            if ($depend1Extras) {
                $depend1 .= ', ' . implode(', ', $depend1Extras);
            }

            if ($depend2Extras) {
                $depend1 .= ',';
                $depend2 = implode(', ', $depend2Extras) . ' only.';
                return [$depend1, $depend2];
            }

            $depend1 .= ' only.';
            return [$depend1, ''];
        }

        // CASE 2: SELF not present (dependent-driven sentence)
        if (empty($data)) {
            return ['', ''];
        }

        $primary = $data[0];
        $primaryRelationRaw = trim((string) ($primary['RELATION'] ?? ''));
        $primaryRelation = strtolower($primaryRelationRaw);
        $primaryAge = $resolve_age($primary, $employee);

        $title = (preg_match('/\b(wife|daughter|mother)\b/', $primaryRelation) ? 'Smt.' : 'Shri.');
        $primaryName = $format_name($primary['NAME'] ?? '');
        $employeeName = $format_name($employee['ENAME'] ?? '');
        $employeeDetails = strtolower(trim((string) ($employee['EDETAILS'] ?? '')));
        $isLate = (stripos($employeeDetails, 'widow') !== false)
            || (stripos($employeeDetails, 'widower') !== false)
            || (stripos($employeeDetails, 'late') !== false);
        $employeeDisplay = trim(($isLate ? 'Late ' : '') . $employeeName);

        $depend1 = "{$title} {$primaryName}";
        if ($is_spouse_relation($primaryRelation)) {
            $depend1 .= ", {$primaryRelation} of {$employeeDisplay}";
        }
        $depend1 .= " aged {$primaryAge} yrs";

        $extras = [];
        if (count($data) > 1) {
            $remaining = array_slice($data, 1);
            $grouped = [];

            foreach ($remaining as $member) {
                $age = $resolve_age($member, $employee);
                $relationKey = strtolower(trim((string) ($member['RELATION'] ?? '')));
                $relationLabel = $format_relation($member['RELATION'] ?? '');
                if (! isset($grouped[$relationKey])) {
                    $grouped[$relationKey] = ['label' => $relationLabel, 'ages' => []];
                }
                if ($age !== '') {
                    $grouped[$relationKey]['ages'][] = $age;
                }
            }

            foreach ($grouped as $group) {
                $ages = array_values(array_filter($group['ages'], static fn($a) => $a !== ''));
                $count = count($ages);
                if ($count === 0) {
                    continue;
                }

                $relationLabel = $group['label'];
                if ($count > 1) {
                    $relationLabel = $pluralize_relation($relationLabel);
                    $extras[] = "{$count} {$relationLabel} aged " . implode(' yrs and ', $ages) . " yrs";
                } else {
                    $extras[] = "{$relationLabel} aged {$ages[0]} yrs";
                }
            }
        }

        if ($extras) {
            $depend1 .= ',';
            $depend2 = implode(', ', $extras) . ' only.';
            return [$depend1, $depend2];
        }

        $depend1 .= ' only.';
        return [$depend1, ''];
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
