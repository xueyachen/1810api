<?php

namespace App\Admin\Controllers;

use App\model\admin_login;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class LoginController extends Controller
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('Index')
            ->description('description')
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('Edit')
            ->description('description')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('Create')
            ->description('description')
            ->body($this->form());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new admin_login);

        $grid->id('Id');
        $grid->username('Username');
        $grid->pass('Pass');
        $grid->email('Email');
        $grid->tel('Tel');
        $grid->card('Card');
        $grid->appid('Appid');
        $grid->appsecret('Appsecret');

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(admin_login::findOrFail($id));

        $show->id('Id');
        $show->username('Username');
        $show->pass('Pass');
        $show->email('Email');
        $show->tel('Tel');
        $show->card('Card');
        $show->appid('Appid');
        $show->appsecret('Appsecret');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new admin_login);

        $form->text('username', 'Username');
        $form->text('pass', 'Pass');
        $form->email('email', 'Email');
        $form->text('tel', 'Tel');
        $form->text('card', 'Card');
        $form->text('appid', 'Appid');
        $form->text('appsecret', 'Appsecret');

        return $form;
    }
}
