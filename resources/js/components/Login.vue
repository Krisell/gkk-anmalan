<template>
  <div>
    <div @click="google" class="loginBtn google font-sans text-sm font-medium" style="text-align: center">
      <div class="icon">
        <svg
          version="1.1"
          xmlns="http://www.w3.org/2000/svg"
          width="18px"
          height="18px"
          viewBox="0 0 48 48"
          class="abcRioButtonSvg"
        >
          <g>
            <path
              fill="#EA4335"
              d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"
            ></path>
            <path
              fill="#4285F4"
              d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"
            ></path>
            <path
              fill="#FBBC05"
              d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"
            ></path>
            <path
              fill="#34A853"
              d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"
            ></path>
            <path fill="none" d="M0 0h48v48H0z"></path>
          </g>
        </svg>
      </div>
      <div class="login-text">Logga in med Google</div>
    </div>

    <div style="margin-top: 10px" @click="microsoft" class="loginBtn microsoft font-sans text-sm font-medium">
      <div class="icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21">
          <title>MS-SymbolLockup</title>
          <rect x="1" y="1" width="9" height="9" fill="#f25022" />
          <rect x="1" y="11" width="9" height="9" fill="#00a4ef" />
          <rect x="11" y="1" width="9" height="9" fill="#7fba00" />
          <rect x="11" y="11" width="9" height="9" fill="#ffb900" />
        </svg>
      </div>
      <div class="login-text">Logga in med Microsoft</div>
    </div>
  </div>
</template>

<script>
import PostRedirect from '../modules/PostRedirect.js'

export default {
  props: ['to'],
  methods: {
    google() {
      firebase.auth().languageCode = 'sv'
      const provider = new firebase.auth.GoogleAuthProvider()
      provider.addScope('email')
      firebase.auth().signInWithPopup(provider).then(result => {
        PostRedirect.send({
            url: '/auth/google',
            data: {
              idToken: result.credential.idToken,
              to: this.to,
            },
          })
      })
    },
    microsoft() {
      firebase
        .auth()
        .signInWithPopup(new firebase.auth.OAuthProvider('microsoft.com'))
        .then((result) => {
          PostRedirect.send({
            url: '/auth/microsoft',
            data: {
              accessToken: result.credential.accessToken,
              to: this.to,
            },
          })
        })
    },
  },
}
</script>

<style scoped lang="less">
.loginBtn {
  cursor: pointer;
  display: flex;
  width: 240px;
  margin: auto;
  height: 41px;
  box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.63);
  justify-content: center;
  align-items: center;
  border-radius: 2px;

  background: #4285f4;
  border: 1px solid #4285f4;

  .icon {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .login-text {
    flex: 1;
    color: white;
  }

  &.microsoft {
    box-shadow: none;
    border: none;
    border-radius: 0;
    background: #2f2f2f;

    .icon {
      margin-left: 12px;
    }

    .login-text {
      text-align: center;
      // margin-left: 12px;
    }
  }

  &.google {
    .icon {
      width: 43px;
      height: 100%;
      background: white;
    }
  }
}

.googleSpinner {
  font-size: 16px;
  margin-right: 10px;
  color: white;
}
</style>
