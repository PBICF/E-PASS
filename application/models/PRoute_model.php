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

	public function check_exact_route()
	{
		$this->db->where('FRSTN', $this->input->post('from_station_code', TRUE));
		$this->db->where('TOSTN', $this->input->post('to_station_code', TRUE));

		$vias = array_filter($this->input->post('via', TRUE));

		foreach ($vias as $i => $via) {
			$this->db->where("VIA" . ($i + 1), $via);
		}

		return $this->db->get($this->table)->row();
	}

	public function save_route()
	{
		$from = $this->input->post('from_station_code', TRUE);
		$to = $this->input->post('to_station_code', TRUE);
		$vias = $this->input->post('via', TRUE);

		$this->db->set('FRSTN', $from);
		$this->db->set('TOSTN', $to);

		$vias = array_filter($vias);
		
		$nextId = $this->next_route_id($from, $to);
		$this->db->set('RSLNO', $nextId);

		foreach ($vias as $i => $via) {
			$this->db->set("VIA" . ($i + 1), $via);
		}

		return $this->db->insert($this->table);
	}

	private function next_route_id($from, $to)
	{
		$query = $this->db->query("
				SELECT NVL(MAX(RSLNO), 0) + 1 AS NEXT_ID
				FROM PROUTES
				WHERE FRSTN = ?
				AND TOSTN = ?
			", array($from, $to));

		return $query->row()->NEXT_ID;
	}
}