<?php

namespace Whole\Core\Http\Controllers\Admin;

use Laracasts\Flash\Flash;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Whole\Core\Repositories\Template\TemplateRepository;
use Whole\Core\Repositories\Block\BlockRepository;
use Whole\Core\Repositories\Content\ContentRepository;
use Whole\Core\Repositories\Component\ComponentRepository;
use Whole\Core\Repositories\ContentPage\ContentPageRepository;
use Whole\Core\Repositories\ContentPage\ContentPageFieldRepository;
use Whole\Core\Repositories\Setting\SettingRepository;
use Whole\Core\Logs\Facade\Logs;
class ContentPagesController extends Controller
{
    protected $template;
    protected $block;
    protected $content;
    protected $component;
    protected $content_page;
    protected $content_page_field;
    protected $setting;

    /**
     * @param TemplateRepository $template
     * @param BlockRepository $block
     * @param ContentRepository $content
     * @param ComponentRepository $component
     * @param ContentPageRepository $content_page
     * @param ContentPageFieldRepository $content_page_field
     * @param SettingRepository $setting
     */
    public function __construct(TemplateRepository $template, BlockRepository $block, ContentRepository $content, ComponentRepository $component,ContentPageRepository $content_page, ContentPageFieldRepository $content_page_field, SettingRepository $setting)
    {
        $this->template = $template;
        $this->block = $block;
        $this->content = $content;
        $this->component = $component;
        $this->content_page = $content_page;
        $this->content_page_field = $content_page_field;
        $this->setting = $setting;
    }


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $content_pages = $this->content_page->all();
        return view('backend::content_pages.index',compact('content_pages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $components = $this->component->allFile();
        $blocks = $this->block->all();
        $contents = $this->content->all();
        $templates = $this->template->all()->lists('name','id');
        $select_template = $this->template->selectTemplate();
        return view('backend::content_pages.create',compact('components','contents','blocks','templates','select_template'))->with('slide_close',true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        return $this->content_page->create($request->all());
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $content_page = $this->content_page->select($id);
        $content_page_field = $this->content_page_field->where('content_page_id',$content_page->id);
        $components = $this->component->allFile();
        $blocks = $this->block->all();
        $contents = $this->content->all();
        $templates = $this->template->all()->lists('name','id');
        $select_template = $this->setting->first()->template;
        $template_fields = $this->content_page->templateFields($id);
        return view('backend::content_pages.edit',compact('content_page_field','content_page','components','contents','blocks','templates','select_template','template_fields'))->with('slide_close',true);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        return $this->content_page->update($request->all(),$id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $message = $this->content_page->delete($id) ?
            ['success','Başarıyla Silindi'] :
            ['error','Bir Hata Meydana Geldi ve Silinemedi'];
        if ($message[0]=="success")
        {
            Logs::add('process',"İçerik Sayfası Silindi\n ID:{$id}");
        }else
        {
            Logs::add('errors',"İçerik Sayfası Silinemedi\n ID:{$id}");
        }
        Flash::$message[0]($message[1]);
        return back();
    }


    /**
     * @param Request $request
     * @return array|string
     */
    public function selectTemplate(Request $request)
    {
//        $this->template->find($request->get('id'))
        return $this->template->templateFields($request->get('id'))?:"false";
    }
}