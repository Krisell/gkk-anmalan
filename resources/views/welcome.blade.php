@extends('layouts.app')

@section('css')
<style>
.actions {
    display: flex;
    margin: 30px;
    flex-direction: row;
}

.actions div {
    margin: 10px;
}

@media (max-width: 1100px) {
  .actions {
    flex-direction: column;
  }
}

</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div style="text-align: center;">
                <div>
                    <img style="height: 200px;" src="https://www.gkk-styrkelyft.se/wp-content/uploads/2014/08/Tv%c3%a5f%c3%a4rg-p%c3%a5-m%c3%b6rk-bakgrund-transparent.png">
                </div>

                <div class="links actions">
                    <action-card description="Tävlingsanmälan för GKK-aktiv" icon="trophy"></action-card>
                    <action-card description="Funktionärsanmälan för GKK-aktiva" icon="users"></action-card>
                    <action-card description="Intresseanmälan från andra föreningar att tävla med GKK" icon="lightbulb-o"></action-card>
                    <action-card description="Admin" icon="lock"></action-card>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
