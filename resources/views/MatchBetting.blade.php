@extends('layouts.test')

@section('title', 'matchbetting')

@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>



<style>

body {
    font-family: Oswald;
}    
.selected {
    background-color: #28a745; 
}

.odds {
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.odds:hover {
    background-color: #f5f5f5;
}
.card-content {
    display: flex;
    align-items: center;
}

.input-group {
    margin-top: 10px;
}

.input-group {
    margin-top: 10px;
}

.input-group {
    margin-top: 10px;
}

.odds-container {
  display: flex;
  justify-content: space-around;
}

.odds-container .odds-item {

}

.odds-container .odds-item label {
  margin-top: 25px;
  cursor: pointer;
  display: inline-block;
  text-align: center;
  vertical-align: top;
  padding: 10px 15px;
  border: 2px solid grey;
  border-radius: 5px;
  transition: background-color 0.3s ease, border-color 0.3s ease;
}
.odds-container .odds-item input[type="radio"] {
  display: none; 
}

.odds-container .odds-item input[type="radio"]:checked + label {
  background-color: #28a745;
  border-color: #28a745;
  color: white;
}
.odds-container .odds-item label:hover {
  background-color: #f5f5f5;
}
.odds-container .odds-item .odd-label-text {
  margin-bottom: 5px; 
  font-size: 14px; 
  position: absolute; /* Add relative positioning to the odds-item */
  bottom: 110px; 
  margin-left: 25px;

}

.card {
    background: #212121;
            box-shadow: 10px 10px 10px rgb(25, 25, 25),
                -10px -10px 10px rgb(60, 60, 60); 
    border: 2px solid orange; 
    color: white;
}

.card-content {
    display: flex;
    align-items: center;
    padding: 15px; 
}

.card-img-top {
    width: 100px; 
    height: 100px;
    margin-right: 10px; 
}

.card-description {
    flex: 1;
}

h5{
    font-family: Oswald;
    font-size: 22px;
}
h1{
    font-family: Oswald;
    font-size: 22px;
    color: white;
    margin-bottom: 30px;
    text-align: center;
}
#betHistoryModal {
    text-align: center;
}
@media (max-width: 767px) {

.btn-primary {
    white-space: nowrap;
    width: auto; 
    }
}


</style>
<div class="container">
<h1>Cryptocurrency Price preditor - We provide the odds, you determine if it will hit the price target or not!</h1>
    <div class="container" style="text-align: right">
    <a href="{{ route('betHistory') }}" class="btn btn-primary" style="margin-bottom: 10px;">History</a>

        </div>
    <div class="row justify-content-center">
        @foreach($betslips as $betslip)
            <div class="col-lg-6 col-md-8 mb-4">
                <div class="card h-100">
                    <form action="{{ route('placeBet', ['betslip' => $betslip->id]) }}" method="POST" class="bet-form" data-betslip-id="{{ $betslip->id }}">
                        @csrf  <!-- CSRF token for Laravel -->
                        <div class="card-content">
                            <img src="{{ asset($betslip->picture) }}" class="card-img-top" alt="{{ $betslip->name }}">
                            <div class="message-container">
                                <div id="message_{{ $betslip->id }}" class="alert"></div>
                            </div>
                            <div class="card-description">
                                <h5>{{ $betslip->description }}</h5>
                            </div>
                        </div>
                        <div class="odds-container">
                            <div class="odds-item">
                            <input type="radio" id="odd_one_{{ $betslip->id }}" name="selected_odd" value="odd_one">
                            <label for="odd_one_{{ $betslip->id }}">{{ $betslip->odd_one }}x</label>
                            <p class="odd-label-text">YES</p>
                            
                            
                            
                            </div>
                            <div class="odds-item">
                            <input type="radio" id="odd_two_{{ $betslip->id }}" name="selected_odd" value="odd_two">
                                <label for="odd_two_{{ $betslip->id }}">{{ $betslip->odd_two }}x</label>
                                <p class="odd-label-text">NO</p>
                               
                              
                               
                            </div>
                            @if ($betslip->odd_three !== null)
                            <div class="odds-item">
                            <input type="radio" id="odd_three_{{ $betslip->id }}" name="selected_odd" value="odd_three">
                            <label for="odd_three_{{ $betslip->id }}">{{ $betslip->odd_three }}x</label>    
                            <p class="odd-label-text">EQUAL</p>
                     
                                
                               
                            </div>
                            @endif
                        </div>
                        @if ($betslip->status !== 'frozen')
                        <div class="input-group mt-3 d-flex justify-content-center">
                            <input type="number" name="bet_amount" class="pl-8 text-white bg-no-repeat h-11 w-24 bg-transparent font-bold" id="bet_amount" required style="background-image: url(/icons/coins3.png); border: 1px solid white; background-size: 24px; background-position: 5px 5px; border-color: rgb(51 53 65 / var(--tw-border-opacity));" placeholder="0.00" value="0.00" min="1" max="{{ $maxBet }}">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">Bet</button>
                            </div>
                        </div>
                        @else
                        <p class="text-white">Bets are now closed</p>
                        @endif
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <h1>Want to have YOUR coin listed here? Contact us on <a href="https://t.me/EthdiceOfficial">Telegram</a>
</h1>
</div>
<div class="modal fade" id="betResultModal" tabindex="-1" role="dialog" aria-labelledby="betResultModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="betResultModalLabel">Bet Placement</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="betResultMessage"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function selectOdd(element) {
    const odds = document.querySelectorAll('.odds');
    odds.forEach(odd => odd.classList.remove('selected'));
    element.classList.add('selected');
}


document.addEventListener('DOMContentLoaded', function() {
    const betForms = document.querySelectorAll('.bet-form');
    betForms.forEach(form => {
        form.addEventListener('submit', validateForm);
    });
});

$('.bet-form').on('submit', function(e) {
    e.preventDefault();

    var form = $(this);
    var betslipId = form.data('betslip-id');

    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: form.serialize(),
        success: function(response) {
            $('#betResultMessage').html('<div class="alert alert-success">' + response.message + '</div>');
            $('#betResultModal').modal('show'); // Show the modal

            // If the response includes the updated coin balance, update it on the page.
            if (response.updated_coins) {
                $('.coin-balance').text(response.updated_coins);
            }
        },
        error: function(xhr, status, error) {
            var response = JSON.parse(xhr.responseText);
            $('#betResultMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
            $('#betResultModal').modal('show'); // Show the modal
        }
    });
});

</script>

@endsection

