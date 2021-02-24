<?php

namespace App\Modules\Core\Library;

class EloquentHelper
{

    /**
     * @param $model
     * @param $slug
     * @return mixed
     */
    public static function getRecordIdBySlug($model, $slug)
    {

        $model = \App::make($model);
        return optional($model->where('slug', $slug)->first())->id;
    }

    /**
     * @param $model
     * @param null $id
     * @return mixed
     */
    public static function findOrMakeInstance($model, $id = null)
    {

        $model_obj = \App::make($model);
        $record    = $model_obj->find($id);

        return $record ?? $model_obj;
    }
}
