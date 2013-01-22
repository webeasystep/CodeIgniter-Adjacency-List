<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Al extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->library('adjacency_list');
        $this->load->helper('adjacency_list');
    }

    public function index()
    {
        $groups = $this->adjacency_list->get_all_groups();
        $data['groups'] = array();
        foreach ($groups as $group)
        {
            $data['groups'][$group['id']] = $group;
            $data['groups'][$group['id']]['items'] = $this->adjacency_list->get_all_by_group($group['id']);
        }
        $data['title'] = 'Navigation';
        $this->load->view('adjacency/list/index', $data);
    }

    public function add($id)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a>', '</div>');
        $this->form_validation->set_rules('name', 'Name', 'required|trim|xss_clean');
        $this->form_validation->set_rules('url', 'URL', 'required|trim|xss_clean');
        $this->form_validation->set_rules('parent_id', 'Parent', 'required|integer|xss_clean');

        if ($this->form_validation->run() == FALSE)
        {
            $data['navigation'] = $this->adjacency_list->get_group($id);
            $data['dropdown'] = $this->adjacency_list->get_all_for_dropdown($id);
            $data['title'] = 'Add item';
            $this->load->view('adjacency/list/add', $data);
        }
        else
        {
            $data = $this->input->post();
            $data['group_id'] = $id;

            $this->adjacency_list->add_item($data);

            redirect('al');
        }
    }

    public function edit($id)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a>', '</div>');
        $this->form_validation->set_rules('name', 'Name', 'required|trim|xss_clean');
        $this->form_validation->set_rules('url', 'URL', 'required|trim|xss_clean');
        $this->form_validation->set_rules('parent_id', 'Parent', 'required|integer|xss_clean');


        if ($this->form_validation->run() == FALSE)
        {
            if ($data = $this->adjacency_list->get_item($id))
            {
                $data['navigation'] = $this->adjacency_list->get_group($data['group_id']);
                $data['dropdown'] = $this->adjacency_list->get_all_for_dropdown($data['group_id'], $id);
                $data['title'] = 'Edit item';
                $this->load->view('adjacency/list/edit', $data);
            }
            else
            {
                show_404();
            }
        }
        else
        {
            $this->adjacency_list->update_item($id, $this->input->post());

            redirect('al');
        }
    }

    public function delete($id)
    {
        if ( ! $this->adjacency_list->has_children($id))
        {
            $this->adjacency_list->delete_item($id);
        }

        redirect('al');
    }

    public function reorder()
    {
        if ($this->input->is_ajax_request())
        {
            $arr = array();
            $order = $this->input->post('order');
            parse_str($order, $arr);

            $this->adjacency_list->reorder($arr['list']);
        }
        else
        {
            show_404();
        }
    }

    public function add_group()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a>', '</div>');
        $this->form_validation->set_rules('name', 'Name', 'required|trim|xss_clean');

        if ($this->form_validation->run() == FALSE)
        {
            $data['title'] = 'Add navigation group';
            $this->load->view('adjacency/group/add', $data);
        }
        else
        {
            $this->adjacency_list->add_group($this->input->post());			

            redirect('al');
        }
    }

    public function edit_group($id)
    {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-error"><a class="close" data-dismiss="alert" href="#">×</a>', '</div>');
        $this->form_validation->set_rules('name', 'Name', 'required|trim|xss_clean');

        if ($this->form_validation->run() == FALSE)
        {
            if ($data = $this->adjacency_list->get_group($id))
            {
                $data['title'] = 'Edit navigation group';
                $this->load->view('adjacency/group/edit', $data);
            }
            else
            {
                show_404();
            }
        }
        else
        {
            $this->adjacency_list->update_group($id, $this->input->post());

            redirect('al');
        }
    }

    public function delete_group($id)
    {
        $this->adjacency_list->delete_group($id);

        redirect('al');
    }

    public function samples()
    {
        $data['title'] = 'Samples';
        $this->load->view('adjacency/sample/index', $data);
    }
}

/* End of file al.php */
/* Location: ./application/controllers/al.php */