
var intervalIDs = [];
function openToken(access_key_id,pagename){ 

    for (var i = 0; i < intervalIDs.length; i++) {
        clearInterval(intervalIDs[i]);
    }

    token_no = access_key_id;
    page = pagename;
  
    const settings = {
      url: 'https://web.rusoft.in/api/checkSchoolToken/' + token_no,
      method: 'GET',
    };
    
    const tokenStatus = {
        url: 'softwareTokenStatus',
        method: 'GET',
    };
    
    function softwareTokenStatus(){
        
        $.ajax(tokenStatus)
         .done(function (response){
            
         })
         .fail(function(xhr, status, error) {
            console.log(xhr.status);
        });
        
    }
    
    if(page == 'login'){
        
        $.ajax(settings)
        .done(function (response) {
            
        
            $('.overlay').removeClass('bg-white');
            $('#noInternetSection').addClass('d-none');
            
            if(response !== ''){
            
            
                $('#studentCount').val(response.data.student_count);
                $('#branchCount').val(response.data.branch_count);
                $('#userCount').val(response.data.user_count);
                $('#schoolName').html(response.data.website_name);
                $('#ownerName').html(response.data.name);
                $('#mobile').html(response.data.mobile);
                $('#validity').html(response.data.last_emc_date + " to " + response.data.emc_date);
                var date1 = new Date(response.data.emc_date);
                var date2 = new Date();
                var difference = Math.abs(date1 - date2);
                var daysDifference = Math.ceil(difference / (1000 * 60 * 60 * 24));
                var formattedAMCDate = date1.getDate().toString().padStart(2, '0') + ' ' +
                                    date1.toLocaleString('en-US', { month: 'short' }).toUpperCase() + ' ' +
                                    date1.getFullYear();
                $('#validityDuration').html(daysDifference + " Days");
                    
                $('#bankName').html(response.contact.bank_name);
                $('#bankIfsc').html(response.contact.bank_ifsc);
                $('#bankAccType').html(response.contact.bank_acc_type);
                $('#bankAccNo').html(response.contact.bank_acc_no);
                $('#bankAccOwner').html(response.contact.bank_acc_owner);
                $('#bankAccOwnerMobile').html(response.contact.bank_acc_owner_mobile);
                
                var supportDivData = '';
                var supportDiv = '';
                var reminder = '';
                
                $.each( response.support, function(index,value){
                    supportDiv = $('#supportDiv').html();
                    supportDiv = supportDiv.replace('Developer', value.designation).replace('Vineet', value.name).replace('Skill', value.skill).replace('8209949186', value.mobile);
                    supportDivData += supportDiv;
                });

                $('#appendSupportDiv').html(supportDivData); 
                
                reminder = response.data.reminder;
                
                
                if(response.data.status == 1 && response.status == 1){
                        
                    $('.overlay-wrapper').addClass('d-none');//Everything OK & Software expiry date is far away
                    $('#bankDetails').addClass('d-none');
                    
                    if(response.expiring == 1){
                        reminder = reminder + ' . Your Software yearly plan (AMC) expires on ' + formattedAMCDate + '. Renew now to continue using all services smoothly.';
                    }
                    
                    softwareTokenStatus();
                    
                }else{
                    $('.overlay-wrapper,#softwareExpiredSection').removeClass('d-none');
                    $('#softwareOwnerDetails').addClass('d-none');
                    $('.overlay').addClass('bg-white');
                    
                }

                if(reminder.length > 0){
                    $('#showNotice').html('<div id="expiryNotice"><p class="mb-0"><i class="fa fa-warning"></i> ' + reminder + '</p></div>');
                }
            
            }else{
                console.log('Please Check In Controller. Error In Getting Response');
                alert('Something Went Wrong | Please Contact To Your Service Provider');
            }
            
        })
        .fail(function(xhr, status, error) {
            if(xhr.status === 0){
                $('.overlay-wrapper,#noInternetSection').removeClass('d-none');
                $('.overlay').addClass('bg-white');
                var internetInterval = setInterval(displayHello, 4000);
                intervalIDs.push(internetInterval);
            }else if(xhr.status === 500){
                console.log('Please Check In Controller. Error In Getting Response');
                alert('Something Went Wrong | Please Contact To Your Service Provider');
            }
            
        });
        
        function displayHello(){
            openToken(token_no,page);
        }
        
    }else{
        alert('Something Went Wrong | Please Contact To Your Service Provider');
    }
    
    
}

function autoPayment(){
    $('.autoPayment').html('Auto Payment is Coming Soon!');
    setTimeout(function(){
        $('.autoPayment').html('Payment');
    }, 4000);
}   

// document.addEventListener('contextmenu', function(e) {
//   e.preventDefault();
// });

// document.onkeydown = function(e) {
//     if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 85 || e.keyCode === 117)) {
//         //alert('Not Allowed');
//         return false;
//     }
// };