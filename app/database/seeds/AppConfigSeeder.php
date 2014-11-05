<?php
	/**
	 * AppConfigSeeder
	 */
	class AppConfigSeeder extends Seeder
	{
		public function run()
		{
			$app_config = new AppConfig;
			$app_config->key = 'address';
			$app_config->value = '123 Nowhere street';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'company';
			$app_config->value = 'Open Source Point of Sale';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'email';
			$app_config->value = 'admin@pappastech.com';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'fax';
			$app_config->value = '';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'phone';
			$app_config->value = '555-555-5555';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'return_policy';
			$app_config->value = 'Test';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'website';
			$app_config->value = '';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'tax';
			$app_config->value = '16';
			$app_config->save();
		}
	}
