/*
	Copyright 2015 Google Inc. All Rights Reserved.

	Licensed under the Apache License, Version 2.0(the "License");
	you may not use this file except in compliance with the License.
	You may obtain a copy of the License at

	http://www.apache.org/licenses/LICENSE-2.0

	Unless required by applicable law or agreed to in writing, software
	distributed under the License is distributed on an "AS IS" BASIS,
	WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	See the License for the specific language governing permissions and
	limitations under the License.
*/

//	define global vars

	var accessToken;
	var protocol	= window.location.protocol;
	var host		= window.location.host;
	var baseURL		= protocol + '//' + host;
	var privacy		= 'public';

//	interval before calling for next polling request

	var STATUS_POLLING_INTERVAL_MILLIS = 10 * 1000;

//	define global input

	var objLogoutButton		= $('#youtube-logout-button');
	var objPropertyIdInput	= $('input[data-role="youtube-property_id-input"]');
	var objSessionIdInput 	= $('input[data-role="youtube-session-input"]');
	var objTitleInput		= $('input[data-role="youtube-title-input"]');
	var objDescInput		= $('input[data-role="youtube-description-input"], textarea[data-role="youtube-description-input"]');
	var objFileInput		= $('input:file[data-role="youtube-file-input"]');
	var objTagInput			= $('input[data-role="youtube-tag-input"], textarea[data-role="youtube-tag-input"]');
	var objUploadButton		= $('input:button[data-role="youtube-upload-button"], button[data-role="youtube-upload-button"]');
	var objAddButton		= $('input:button[data-role="youtube-add-button"], button[data-role="youtube-add-button"]');

	if(objDescInput.length <= 0){
		objDescInput = objTitleInput;
	}

//	result placeholders

	var objChannelName		= $('#channel-name');
	var objChannelThumbnail	= $('#channel-thumbnail');
	var objProgressStatus	= $('#post-upload-status');
	var objPreSignInBlock	= $('.pre-sign-in');
	var objPostSigninBlock	= $('.post-sign-in');

//	"signinCallback" ini dipanggil 2 kali, 
//	1. saat page load (liat data-callback di button sign-in google)
//	2. saat authentication popup google selesai
/*
	var signinCallback = function(result){
		var token = result.access_token ? result.access_token : $('body').attr('data-youtube-access-token');

		if(token){
			var uploadVideo	= new UploadVideo();

		//	set global access token
			accessToken	= token;

		//	renew attr value
			$('body').attr('data-youtube-access-token', accessToken);

		//	authenticate on uploadVideo ready
			uploadVideo.ready(accessToken);
		}
	};
*/
/**
 * YouTube video uploader class
 *
 * @constructor
 */
 
	accessToken = document.getElementsByTagName('body')[0].getAttribute('data-youtube-token');

	if(accessToken && typeof signinCallback == 'function'){
		signinCallback({
			'result' : {
				'access_token' : accessToken, 
			}
		});
	}
 
	var UploadVideo = function(){
		var tags;

		if(objTagInput.length && objTagInput.val() != ''){
			tags = objTagInput.val().split(',');
		}
		else{
			tags = [ 'Prime System', 'Diunggah dari Prime System', 'http://www.primesystem.id/', ];
		}

		this.tags				= tags;
		this.categoryId			= 22;
		this.videoId			= '';
		this.uploadStartTime	= 0;
	};

	UploadVideo.prototype.ready = function(accessToken){
		this.accessToken	= accessToken;
		this.gapi			= gapi;
		this.authenticated	= true;

		this.gapi.client.request({
			path		: '/youtube/v3/channels',
			params		: {
				part	: 'snippet',
				mine	: true
			},
			callback	: function(response){
				if(response.error){
					console.log(response.error.message);
				}
				else{
				//	try to get user profile from google plus
					var useYoutube	= true;
					var clientID	= $('#signInButton').data('clientid');
					var apiKey		= $('#signInButton').data('key');
					var scopes		= $('#signInButton').data('scope');

				//	this.gapi.client.setApiKey(apiKey);
				//	this.gapi.auth.authorize({
				//		client_id	: clientID, 
				//		scope		: scopes, 
				//		immediate	: true
				//	}, function(authResult){
				//		if(authResult && !authResult.error){
				//			this.gapi.client.load('plus', 'v1', function(){
				//				var request = this.gapi.client.plus.people.get({
				//					'userId': 'me'
				//				});

				//				request.execute(function(resp){
				//					var profilePic	= resp.image.url ? resp.image.url + '&sz=100' : '';
				//					var profileName	= resp.displayName ? resp.displayName : '';

				//					if(profilePic || profileName){
				//						objChannelThumbnail.attr('src', profilePic);
				//						objChannelName.text(profileName);

				//						useYoutube = false;
				//					}
				//				});
				//			});
				//		}
				//	});

				//	console.log('YouTube response');
				//	console.log(response);

					if(useYoutube){
					//	youtube profile not used, then use data from youtube instead
						var responseItems	= response.items[0] || {};
						var snippet			= responseItems.snippet || {};
						var channelTitle	= snippet.title || 'Untitled Channel';
						var channelThumb	= snippet.thumbnails || '';
						var thumbnailURL	= channelThumb.medium.url || '';

						objChannelName.text(channelTitle);
						objChannelThumbnail.attr('src', thumbnailURL);
					}

					objPreSignInBlock.hide();
					objPostSigninBlock.show();
				}
			}.bind(this)
		});

		objUploadButton.on('click', this.handleUploadClicked.bind(this));
	};

/**
 * Uploads a video file to YouTube.
 *
 * @method uploadFile
 * @param{object}file File object corresponding to the video to upload.
 */
	UploadVideo.prototype.uploadFile = function(file){
		if(objDescInput.length <= 0){
			objDescInput = objTitleInput;
		}

		var title		= objTitleInput.val();
		var description = objDescInput.val().toString();
			description	= description.split('<break>').join("\n");

		var metadata = {
			snippet	: {
				title		: title != '' ? title : 'Untitled',
				description	: description,
				tags		: this.tags,
				categoryId	: this.categoryId
			},
			status	: {
				privacyStatus : privacy
			}
		};

	//	console.log(metadata);

		var uploader = new MediaUploader({
			baseUrl		: 'https://www.googleapis.com/upload/youtube/v3/videos',
			file		: file,
			token		: this.accessToken,
			metadata	: metadata,
			params		: {
				part : Object.keys(metadata).join(',')
			},
			onError		: function(data){
			//	disini
			//	clearYoutubeInput();

				console.log(data);

				var message = data;
				try{
					var errorResponse	= JSON.parse(data);
					var errorCode		= errorResponse.error.code;
						message			= errorResponse.error.message;

					if(errorCode == 400 || errorCode == 401){
						var errorDetail	= errorResponse.error.errors[0];
						var errorReason	= errorDetail.reason;
							message		= describeErrorCode(errorReason);

						console.log('Code : ' + errorCode + ', ' + errorReason);
					}
				}
				finally{
					if(errorReason == 'badContent'){
						$.toggleErrorInput({
							obj		: objFileInput, 
							isError	: true, 
							message	: message
						});	
					}
					else{
						if(message != ''){
							alert(message);
						}

						if(errorReason == 'youtubeSignupRequired'){
							var channelLink = $('#create-channel-link');

							if(channelLink.length){
								$('html, body').animate({
									scrollTop : channelLink.offset().top
								}, 2000);
							}
						}
					}
				}

				objUploadButton.prop('disabled', false);
			}.bind(this),
			onProgress	: function(data){
				var currentTime		= Date.now();
				var bytesUploaded	= data.loaded;
				var totalBytes		= data.total;

			//	The times are in millis, so we need to divide by 1000 to get seconds.
				var bytesPerSecond				= bytesUploaded / ((currentTime - this.uploadStartTime) / 1000);
				var estimatedSecondsRemaining	= (totalBytes - bytesUploaded) / bytesPerSecond;
				var percentageComplete			= (bytesUploaded * 100) / totalBytes;

				$('#upload-progress').attr({
					value	: bytesUploaded,
					max		: totalBytes
				});

				percentageComplete = percentageComplete.toFixed(2);

				if(percentageComplete == 100){
					percentageComplete == 100;
				}

				bytesUploaded	= (bytesUploaded / 1024).toFixed(2);
				totalBytes		= (totalBytes / 1024).toFixed(2);

				$('#percent-transferred').text(percentageComplete);
				$('#bytes-transferred').text(bytesUploaded);
				$('#total-bytes').text(totalBytes);

				$('.during-upload').show();
			}.bind(this),
			onComplete	: function(data){
				var uploadResponse	= JSON.parse(data);
					this.videoId	= uploadResponse.id;

			//	console.log('upload response : ');
			//	console.log(uploadResponse);
			//	console.log('---------------------------------');

				$('.post-upload').show();

				this.pollForVideoStatus();

				$('.during-upload').fadeOut(400, function(){
					$('#percent-transferred').text('');
					$('#bytes-transferred').text('');
					$('#total-bytes').text('');
				});
			}.bind(this)
		});

	//	This won't correspond to the *exact* start of the upload, but it should be close enough.
		this.uploadStartTime = Date.now();
		uploader.upload();
	};

	UploadVideo.prototype.handleUploadClicked = function(e){
	//	we're using ajax here so make sure that the button doesn't trigger any form submit.
		e.preventDefault();

		var self	= $(e.target);
		var isValid	= validateForm(self.closest('form'));

		if(isValid){
			var objFile	= objFileInput.get(0).files[0];

			if(typeof objFile != 'undefined'){
				objUploadButton.prop('disabled', true);
				objProgressStatus.html('').hide();
				this.uploadFile(objFile);
			}
		}
	};

	var pollCount = 0;

	UploadVideo.prototype.pollForVideoStatus = function(){
		this.gapi.client.request({
			path		: '/youtube/v3/videos',
			params		: {
				part	: 'status,player',
				id		: this.videoId
			},
			callback	: function(response){
			//	console.log('raw response : ');
			//	console.log(response);
			//	console.log('---------------------------------');

				if(response.error){
				//	The status polling failed.
					console.log(response.error.message);
					setTimeout(this.pollForVideoStatus.bind(this), STATUS_POLLING_INTERVAL_MILLIS);
				}
				else{
					var responseItems	= response.items[0];
					var uploadStatus	= '';
					var statusMessage	= '';

					if(responseItems){
						uploadStatus = responseItems.status.uploadStatus;
					}

					var loadingMessage = '<img data-role="loader" alt="loading" src="' + baseURL + '/img/loading.gif">';
						loadingMessage+= '<div>Menunggu verifikasi dari YouTube. Mohon tunggu...</div>';
						loadingMessage+= '<div>(Kurang lebih 30 detik, tergantung dari respon yang diberikan dari YouTube)</div>';

					switch(uploadStatus){
						case 'uploaded':
							statusMessage = loadingMessage;

						//	This is a non-final status, so we need to poll again.
							setTimeout(this.pollForVideoStatus.bind(this), STATUS_POLLING_INTERVAL_MILLIS);
						break;

						case 'processed' :
							statusMessage = '<b>Proses Selesai</b>.';

						//	save video to database
							var propertyId = '';

							if(objPropertyIdInput.length <= 0){
								propertyId = $('#property-id').val();
							}
							else{
								propertyId = objPropertyIdInput.val();
							}

						//	reset input included inside
							$.savePropertyVideo({
								'property_id'	: propertyId,
								'video_id'		: this.videoId, 
								'title'			: objTitleInput.val(), 
								'session_id'	: objSessionIdInput.val(), 
							});
						break;

						case 'rejected' :
							var rejectionReason	= responseItems.status.rejectionReason;
								rejectionReason	= describeErrorCode(rejectionReason);
								statusMessage	= '<b>' + rejectionReason + '</b>';

						//	reset input state
							clearYoutubeInput();
						break;

						case 'failed' :
							var failureReason = responseItems.status.failureReason;
								failureReason = describeErrorCode(failureReason)
								statusMessage = '<b>' + failureReason + '</b>';

						//	reset input state
							clearYoutubeInput();
						break;

						default :
						//	console.log(uploadStatus);
						//	console.log(responseItems);

							if(typeof responseItems == 'undefined' && pollCount < 3){
							//	poll count increment
								pollCount++;

								statusMessage = loadingMessage;

							//	sometimes google gives no items (null), so we have to poll again
								console.log('no item given, try to re-poll (' + pollCount + ')');
								setTimeout(this.pollForVideoStatus.bind(this), STATUS_POLLING_INTERVAL_MILLIS);
							}
							else{
							//	All other statuses indicate a permanent transcoding failure.
								statusMessage = '<div>Transcoding gagal.</div>';
								statusMessage+= '<div>Anda dapat melihat status video yang Anda upload <a href="https://www.youtube.com/my_videos" target="blank">disini</a>.</div>';

							//	reset poll count
								pollCount = 0;

							//	reset input state
								clearYoutubeInput();
							}
						break;
					}

				//	display result status
					objProgressStatus.html('<li>' + statusMessage + '</li>').fadeIn(400);

					if(objProgressStatus.find('a').length){
						objProgressStatus.find('a').on('click', function(event){
							objProgressStatus.html('').hide();
						});
					}

					if(uploadStatus == 'processed'){
						setTimeout(function(){
							objProgressStatus.fadeOut(400, function(){
								$(this).html('');
							});
						}, 5000);
					}
				}
			}.bind(this)
		});
	};

	function disconnectUser(){
		var logoutURL	= 'https://accounts.youtube.com/accounts/Logout2?hl=en';// + accessToken;
		var logoutFrame	= $(document.createElement('iframe'));

		logoutFrame.attr({
			'src'	: logoutURL, 
			'style'	: 'display:none;', 
		});

		$('body').append(logoutFrame);

		objPostSigninBlock.hide().find('#channel-thumbnail, #channel-name').attr('src', '').html('');
		objPreSignInBlock.show();

		logoutFrame.on('load', function(){
			console.log('loaded');
			$(this).remove();
		});
	}

//	describe what given error code mean (see https://developers.google.com/youtube/v3/docs/videos#properties for all aavailable properties)
	function describeErrorCode(errorCode){
		var errorMessage = '';

		if(errorCode){
			switch(errorCode){
			//	general error
				case 'youtubeSignupRequired' : 
					errorMessage = 'Sepertinya Anda belum memiliki Channel YouTube, ';
					errorMessage+= 'mohon untuk membuat Channel YouTube sebelum melanjutkan (https://www.youtube.com/create_channel).';
					break;
				case 'invalidFilename' : 
					errorMessage = 'Nama file video salah.';
					break;
				case 'invalidTitle' : 
					errorMessage = 'Judul tidak valid.';
					break;
				case 'badContent' : 
					errorMessage = 'Format file tidak sesuai.';
					break;

			//	rejection reason
				case 'claim' : 
					errorMessage = 'Video ditolak terkait masalah klaim.';
					break;
				case 'copyright' : 
					errorMessage = 'Video ditolak terkait masalah hak cipta.';
					break;
				case 'duplicate' : 
					errorMessage = 'Video sudah pernah di-upload sebelumnya.';
					break;
				case 'legal' : 
					errorMessage = 'Video ditolak terkait masalah legal.';
					break;
				case 'length' : 
					errorMessage = 'Durasi video tidak sesuai atau terlalu panjang.';
					break;
				case 'termsOfUse' : 
					errorMessage = 'Video tidak sesuai dengan <a href="http://www.youtube.com/t/terms">Persyaratan Layanan Youtube</a>.';
					break;
				case 'trademark' : 
					errorMessage = 'Video melanggar hak cipta.';
					break;
				case 'uploaderAccountClosed' : 
					errorMessage = 'Akun YouTube Anda sudah ditutup.';
					break;
				case 'uploaderAccountSuspended' : 
					errorMessage = 'Akun YouTube Anda dalam masa penangguhan.';
					break;

			//	failure reason
				case 'codec' : 
					errorMessage = 'Kesalahan pada Codec.';
					break;
				case 'conversion' : 
					errorMessage = 'Kesalahan pada saat konversi.';
					break;
				case 'emptyFile' : 
					errorMessage = 'File kosong.';
					break;
				case 'invalidFile' : 
					errorMessage = 'Jenis file tidak valid.';
					break;
				case 'tooSmall' : 
					errorMessage = 'Video terlalu kecil.';
					break;
				case 'uploadAborted' : 
					errorMessage = 'Upload dibatalkan.';
					break;
			}
		}

		return errorMessage;
	}

	function validateForm(objForm){
		var isError = false;

		if(objForm.length){
			var isFileError	= false;
			var message		= '';
			var extension	= objFileInput.val().split('.').pop().toLowerCase();

			if(objFileInput.val() == ''){
				isFileError	= true;
				message		= 'Mohon pilih video yang akan diunggah.';
			}
			else{
				if($.inArray(extension, ['mov', 'mp4', 'm4a', 'm4p', 'm4b', 'm4r', 'm4v', 'avi', 'wmv', 'mpg', 'mpeg', 'm2p', 'ps', 'flv', '3gp', 'webm']) == -1){
					isFileError	= true;
					message		= 'File yang Anda pilih tidak valid.';
				}
			}

			$.toggleErrorInput({
				obj		: objFileInput, 
				isError	: isFileError, 
				message	: message
			});

			var isTitleError = objTitleInput.val() == '';

			$.toggleErrorInput({
				obj		: objTitleInput, 
				isError	: isTitleError, 
				message	: 'Mohon isi judul video yang akan diunggah.'
			});

			var isError = isFileError || isTitleError;
		}

	//	negasi dari isError, jadi kalo isError === true return valid nya jadi false
		return !isError;
	}

	function clearYoutubeInput(){
		objTitleInput.val('');
		objFileInput.val('');
		objUploadButton.prop('disabled', false);
	}

//	save video handler
	var savePropertyVideoJXHR;

	$.savePropertyVideo = function(saveData){
		if(savePropertyVideoJXHR){
		//	kill last process
			savePropertyVideoJXHR.abort();
		}

		savePropertyVideoJXHR = $.ajax({
			url		: baseURL + '/ajax/save_property_video', 
			type	: 'post', 
			data	: saveData, 
			success	: function(data){
				var data	= $.parseJSON(data);
				var status	= data.status;
				var message	= data.msg;

				if(status == 'success'){
					var videoPlaceholder = $('#youtube-video-placeholder');

					if(videoPlaceholder.length){
						$.directAjaxLink({
							obj: videoPlaceholder,
						});
					}
				}

				clearYoutubeInput();
				console.log(message);

				return (status == 'success');
			}, 
			error	: function(jqXHR, textStatus, errorThrown){
				if(errorThrown != 'abort'){
					alert('Ups, sepertinya terjadi kesalahan saat proses, silakan coba lagi.');
					console.log('An error occured : ' + textStatus + ', '+ errorThrown);
					return false;
				}
			}
		});
	}

$(document).ready(function(){
//	reset state onload
	clearYoutubeInput();

	objLogoutButton.click(function(){
		disconnectUser();
	});

	objUploadButton.click(function(){
		if(accessToken == '' || typeof accessToken == 'undefined'){
			alert('Anda harus login dengan menggunakan akun Google Anda sebelum melanjutkan.');
			console.log('Unauthorized Process.');
		}
	});
});