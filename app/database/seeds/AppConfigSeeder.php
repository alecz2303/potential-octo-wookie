<?php
	/**
	 * AppConfigSeeder
	 */
	class AppConfigSeeder extends Seeder
	{
		public function run()
		{
			DB::table('app_config')->truncate();

			$app_config = new AppConfig;
			$app_config->key = 'address';
			$app_config->value = '123 Nowhere street';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'company';
			$app_config->value = 'Kerberos IT Services Point of Sale';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'email';
			$app_config->value = 'kerberos.it.s@gmail.com';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'fax';
			$app_config->value = '';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'phone';
			$app_config->value = '961 112 0913';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'return_policy';
			$app_config->value = 'Test';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'website';
			$app_config->value = 'www.kerberosits.esy.es';
			$app_config->save();

			$app_config = new AppConfig;
			$app_config->key = 'tax';
			$app_config->value = '16';
			$app_config->save();
		}
	}
