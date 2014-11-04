<?php

class AppConfigController extends PosDashboardController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getIndex()
	{
		$title = 'Configuración';
		$app_config = AppConfig::all();
		return View::make('pos/app_config/index', compact('title','app_config'));
	}

	public function postIndex()
	{
		$app_config = AppConfig::all();

		foreach ($app_config as $key => $value) {
			$field = AppConfig::where('key','=',$value->key)->first();
			$field->value = Input::get($field->key);
			$field->save();
		}
		return Redirect::to('pos/appconfig')->with('success', 'Se han actualizado los datos con éxito');
	}

}
