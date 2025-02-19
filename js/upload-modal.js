$(document).ready(function () {
  let uploadCallback = null;

  $(document).on("uploadPhoto", function (event, callback) {
      uploadCallback = callback;
      $("#photoUploadForm")[0].reset();
      $("#progressBar").hide().attr("value", 0);
      $("#previewImage").hide().attr("src", ""); // Clear preview
      $("#uploadPhotoModal").modal("show");
  });

  // Handle file selection and show thumbnail preview
  $("#photoFile").on("change", function () {
      const file = this.files[0];
      if (file) {
          previewImage(file);
      }
  });

  $("#submitPhoto").on("click", async function (event) {
      event.preventDefault(); // Prevent default form submission

      const description = $("#photoDescription").val();
      const fileInput = $("#photoFile")[0].files[0];

      if (!description || !fileInput) {
          alert("Please enter a description and select a photo.");
          return;
      }

      try {
          const compressedFile = await resizeAndCompressImage(fileInput);
          const response = await uploadFile(description, compressedFile);

          if (uploadCallback) {
              uploadCallback(response);
          }

          $("#uploadPhotoModal").modal("hide");
      } catch (error) {
          alert("Error uploading file: " + error.message);
      }
  });

  function previewImage(file) {
      const reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onload = function (event) {
          $("#previewImage").show().attr("src", event.target.result);
      };
  }

  async function resizeAndCompressImage(file) {
      return new Promise((resolve, reject) => {
          const maxSize = 1000;
          const quality = 0.7;

          const reader = new FileReader();
          reader.readAsDataURL(file);
          reader.onload = function (event) {
              const img = new Image();
              img.src = event.target.result;
              img.onload = function () {
                  const canvas = document.createElement("canvas");
                  let width = img.width;
                  let height = img.height;

                  if (width > height) {
                      if (width > maxSize) {
                          height *= maxSize / width;
                          width = maxSize;
                      }
                  } else {
                      if (height > maxSize) {
                          width *= maxSize / height;
                          height = maxSize;
                      }
                  }

                  canvas.width = width;
                  canvas.height = height;
                  const ctx = canvas.getContext("2d");
                  ctx.drawImage(img, 0, 0, width, height);

                  canvas.toBlob((blob) => {
                      if (!blob) {
                          reject(new Error("Compression failed"));
                          return;
                      }
                      const compressedFile = new File([blob], file.name, {
                          type: "image/jpeg",
                          lastModified: Date.now(),
                      });
                      resolve(compressedFile);
                  }, "image/jpeg", quality);
              };
          };
          reader.onerror = () => reject(new Error("File reading failed"));
      });
  }

  async function uploadFile(description, file) {
      return new Promise((resolve, reject) => {
          const formData = new FormData();
          formData.append("description", description);
          formData.append("file", file);

          $.ajax({
              url: "/api/post.photo-upload.php",
              type: "POST",
              data: formData,
              processData: false,
              contentType: false,
              xhr: function () {
                  let xhr = new window.XMLHttpRequest();
                  xhr.upload.addEventListener("progress", function (evt) {
                      if (evt.lengthComputable) {
                          let percentComplete = (evt.loaded / evt.total) * 100;
                          $("#progressBar").show().attr("value", percentComplete);
                      }
                  }, false);
                  return xhr;
              },
              success: function (response) {
                  resolve(response);
              },
              error: function (jqXHR, textStatus, errorThrown) {
                console.error("Upload failed: ", textStatus, errorThrown);
                console.error("Response text: ", jqXHR.responseText);
                reject(new Error("Upload failed: " + textStatus + " - " + errorThrown));
            }
          });
      });
  }
});