@extends('layouts.modal')
{{-- Content --}}
@section('content')

	{{-- Create User Form --}}
	<form class="form-horizontal" method="post" action="@if (isset($customers)){{ URL::to('pos/customers/' . $customers->people_id . '/edit') }}@endif" autocomplete="off" data-abide>
		<!-- CSRF Token -->
		<input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
		<!-- ./ csrf token -->

		<!-- Tabs -->
		<dl class="tabs" data-tab>
			<dd class="active"><a href="#panel1">General</a></dd>
		</dl>

		<!-- Tabs Content -->
		<div class="tabs-content">
			<!-- General tab -->
			<div class="content active" id="panel1">
				<div class="row">
					<!-- first_name -->
					<div class="large-6 columns">
						<label for="first_name">Nombre(s) <small>Obligatorio</small>
							<input required pattern="[a-zA-Z]+" type="text" name="first_name" id="first_name" value="{{{ Input::old('first_name', isset($people) ? $people->first_name : null) }}}" />
						</label>
						<small class="error">El nombre es requerido y de tipo texto.</small>
					</div>
					<!-- ./ first_name -->

					<!-- last_name -->
					<div class="large-6 columns">
						<label for="last_name">Apellidos <small>Obligatorio</small>
							<input required pattern="[a-zA-Z]+" type="text" name="last_name" id="last_name" value="{{{ Input::old('last_name', isset($people) ? $people->last_name : null) }}}" />
						</label>
						<small class="error">El Apellido es requerido y de tipo texto.</small>
					</div>
					<!-- ./ last_name -->
				</div>

				<div class="row">
					<!-- Email -->
					<div class="large-6 columns">
						<label>Email
							<input type="email" name="email" id="email" value="{{{ Input::old('email', isset($people) ? $people->email : null) }}}" />
						</label>
						<small class="error">No es una dirección de correo valida.</small>
					</div>
					<!-- ./ email -->

					<!-- phone_number -->
					<div class="large-6 columns">
						<label for="phone_number">Teléfono
							<input pattern="number" type="text" name="phone_number" id="phone_number" value="{{{ Input::old('phone_number', isset($people) ? $people->phone_number : null) }}}" />
						</label>
						<small class="error">No es un teléfono valido.</small>
					</div>
					<!-- ./ phone_number -->
				</div>

				<div class="row">
					<!-- address_1 -->
					<div class="large-6 columns">
						<label for="address_1">Dirección 1
							<input type="text" name="address_1" id="address_1" value="{{{ Input::old('address_1', isset($people) ? $people->address_1 : null) }}}" />
						</label>
					</div>
					<!-- ./ address_1 -->

					<!-- address_2 -->
					<div class="large-6 columns">
						<label for="address_2">Dirección 2
							<input type="text" name="address_2" id="address_2" value="{{{ Input::old('address_2', isset($people) ? $people->address_2 : null) }}}" />
						</label>
					</div>
					<!-- ./ address_2 -->
				</div>

				<div class="row">
					<!-- city -->
					<div class="large-6 columns">
						<label for="city">Ciudad
							<input type="text" name="city" id="city" value="{{{ Input::old('city', isset($people) ? $people->city : null) }}}" />
						</label>
					</div>
					<!-- ./ city -->

					<!-- state -->
					<div class="large-6 columns">
						<label for="state">Estado
							<input type="text" name="state" id="state" value="{{{ Input::old('state', isset($people) ? $people->state : null) }}}" />
						</label>
					</div>
					<!-- ./ state -->
				</div>

				<div class="row">
					<!-- zip -->
					<div class="large-6 columns">
						<label for="zip">C.P.
							<input type="text" name="zip" id="zip" value="{{{ Input::old('zip', isset($people) ? $people->zip : null) }}}" />
						</label>
					</div>
					<!-- ./ zip -->

					<!-- country -->
					<div class="large-6 columns">
						<label for="country">País
							<input type="text" name="country" id="country" value="{{{ Input::old('country', isset($people) ? $people->country : null) }}}" />
						</label>
					</div>
					<!-- ./ country -->
				</div>

				<div class="row">
					<!-- comments -->
					<div class="large-12 columns">
						<label for="comments">Comentarios
							<textarea name="comments" id="comments">{{{ Input::old('comments', isset($people) ? $people->comments : null) }}}</textarea>
						</label>
					</div>
					<!-- ./ comments -->
				</div>

				<div class="row">
					<!-- account -->
					<div class="large-6 columns">
						<label for="account">Cuenta #
							<input type="text" name="account" id="account" value="{{{ Input::old('account_number', isset($customers) ? $customers->account_number : null) }}}" />
						</label>
					</div>
					<!-- ./ account -->

					<!-- Taxable -->
					<div class="switch round large-6 columns">
						Gravable
						<input id="taxable" type="checkbox" name="taxable" {{{ Input::old('taxable', isset($customers->taxable) ?  "checked" : null) }}}>
						<label for="taxable">Gravable</label>
					</div>
					<!-- Taxable -->
				</div>
			</div>
			<!-- ./ general tab -->
		</div>
		<!-- ./ tabs content -->
		<div class="row">
		<!-- Form Actions -->
			<element class="button secondary close_popup">Cancel</element>
			<button type="submit" class="button success">OK</button>
		<!-- ./ form actions -->
		</div>
	</form>
@stop
