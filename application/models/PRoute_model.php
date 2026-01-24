<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PRoute_model extends CI_Model 
{
	
	private string $table = 'PROUTES';

	public function get_routes()
	{
		$query = $this->db->get($this->table);
		return $query->result();
	}

    public function get_route_between($origin, $destination)
    {
        return $this->db->get_where($this->table, array('FRSTN' => $origin, 'TOSTN' => $destination))
            ->result_array();
    }
}