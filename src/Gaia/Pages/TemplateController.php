<?php namespace Gaia\Pages;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

//Repositories
use Gaia\Repositories\ComponentTypeRepositoryInterface;
use Gaia\Repositories\TemplateRepositoryInterface;
//Requests
use Gaia\News\TemplateRequest;
//Facades
use Redirect;
use Input;
//Models
use App\Models\Section;
use App\Models\Component;


class TemplateController extends Controller {

	protected $componentTypeRepos, $templateRepos;
	

	public function __construct(ComponentTypeRepositoryInterface $componentTypeRepos, TemplateRepositoryInterface $templateRepos)
	{
		$this->componentTypeRepos = $componentTypeRepos;
		$this->templateRepos = $templateRepos;
	}


	/**
	 * List all the templates
	 * @return type
	 */
	public function index()
	{
		$templates = $this->templateRepos->getAll();
		return view('admin.templates.index', ['templates' => $templates]);
	}
	
	/**
	 * Show the form for creating a new page template.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('admin.templates.create');
	}

	
	/**
	 * Save the page template
	 *  
	 * @return Response
	 */
	public function store(TemplateRequest $request)
	{
		$input = $request->all();
		$template = $this->templateRepos->create($input); 

		return Redirect::route('admin.pages.templates.build', $template->id);
	}


	/**
	 * Build the page custom components
	 * @param int $templateId 
	 * @return Response
	 */
	public function build($templateId)
	{
		$template = $this->templateRepos->find($templateId);
		$component_types = $this->componentTypeRepos->getAll();
		$sections = $this->templateRepos->getSectionsByOrder($templateId);
		$add_section_url = route('admin.pages.templates.add-section', $templateId);
		$reorder_sections_url = route('admin.pages.templates.reorder-sections', $templateId);
		$add_component_url = route('admin.pages.templates.add-component', $templateId);
		$reorder_components_url = route('admin.pages.templates.reorder-components', $templateId);

		$data = [ 
					'component_types'      => $component_types, 
					'sections'			   => $sections,
					'template' 			   => $template, 
					'add_section_url' 	   => $add_section_url,
					'reorder_sections_url' => $reorder_sections_url,
					'add_component_url'    => $add_component_url,
					'reorder_components_url' => $reorder_components_url
			    ];

		return view('admin.templates.build', $data);
	}


	/**
	 * Add a section to the template
	 * @param type $templateId 
	 * @return type
	 */
	public function storeSection($templateId)
	{
		$section = $this->templateRepos->addEmptySection($templateId);
		return $section->render();
	}


	/**
	 * Updates the section title
	 * @return type
	 */
	public function updateSectionTitle($templateId)
	{
		 $inputs = Input::all();
         $section = $this->templateRepos->findSection($inputs['pk']);
         $section->title = $inputs['value'];
         $section->save();
	}


	/**
	 * Reorder sections inside the template
	 * @param type $templateId 
	 * @return type
	 */
	public function reorderSections($templateId)
	{
		$inputs = Input::all();
		if(isset($inputs['data']) && count($inputs['data']))
		{
			foreach($inputs['data'] as $order => $id)
			{
				$section = $this->templateRepos->findSection($id);
				$section->order = $order;
				$section->save();
			}
		}
	}


	/**
	 * Adds a component into a section
	 * @param type $templateId 
	 * @return type
	 */
	public function storeComponent($templateId)
	{
		$inputs = Input::all();
		$component = $this->templateRepos->addComponent($inputs);
		return $component->render();
	}


	/**
	 * Update a component title
	 * @param type $templateId 
	 * @return type
	 */
	public function updateComponentTitle($templateId)
	{
		 $inputs = Input::all();
         $component = $this->templateRepos->findComponent($inputs['pk']);
         $component->title = $inputs['value'];
         $component->save();
	}


	/**
	 * Reorder components inside a section
	 * @param type $templateId 
	 * @return type
	 */
	public function reorderComponents($templateId)
	{
		$inputs = Input::all();
		
		if(isset($inputs['data']) && count($inputs['data']))
		{
			foreach($inputs['data'] as $order => $id)
			{
				$id = (int)str_replace("cp_", "", $id);
				$component = $this->templateRepos->findComponent($id);
				$component->order = $order;
				$component->save();
			}
		}
	}


	/**
	 * Delete a component
	 * @param type $templateId 
	 * @return type
	 */
	public function destroyComponent($templateId)
	{
		$inputs = Input::all();
		$component = $this->templateRepos->findComponent($inputs['id']);
		$component->delete();
	}


	/**
	 * Delete a section
	 * @param type $templateId 
	 * @return type
	 */
	public function destroySection($templateId)
	{
		$inputs = Input::all();
		$section = $this->templateRepos->findSection($inputs['id']);
		$section->delete();
	}


	/**
	 * Updates the component options
	 * @param type $templateId 
	 * @return type
	 */
	public function updateComponentOptions($templateId)
	{
		$inputs = Input::all();
		$component = $this->templateRepos->findComponent($inputs['pk']);
        $component->options = $inputs['value'];
        $component->save();
	}


	/**
	 * Delete a page template
	 * @param type $templateId 
	 * @return type
	 */
	public function destroy($templateId)
	{
		$template = $this->templateRepos->find($templateId);
		$template->delete();
	}
	

}