@extends('layouts.clinica')

@section('title', 'Panel de Control - Asistente')

@section('content')
    <h1>Agenda del Día</h1>
    <section class="dashboard-cards">
        <div class="card card-orange">
            <h3>Citas por confirmar</h3>
            <p class="number">5</p>
        </div>
        <div class="card card-blue">
            <h3>Pacientes en sala</h3>
            <p class="number">2</p>
        </div>
    </section>
    @endsection