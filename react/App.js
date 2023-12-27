// src/App.js
import React from 'react';
import MasterComponent from "./master";
import { ToastContainer } from 'react-toastify';
import {BrowserRouter as Router, Route, Switch, Navigate} from 'react-router-dom';
import Notes from './Notes/Notes'
import SignUpForm from "./User/SignUpForm";
import { createTheme, ThemeProvider } from '@mui/material/styles';
import LoginForm from "./User/Login";
import useAuth from "./Auth/AuthContext";
import Routes from "./RouteGurd";
import AuthProvider from "./Auth/AuthContext";

const PrivateRoute = ({ element: Element, ...rest }) => {
    const { token } = useAuth();
    console.log(token)
    return (
        <Route
            {...rest}
            element={token ? <Element /> : <Navigate to="/login" />}
        />
    );
};

const App = () => {
    return (
        <>
            <ToastContainer />
            <ThemeProvider theme={theme}>
                <AuthProvider>
                    <Routes/>
                </AuthProvider>
            </ThemeProvider>
        </>
    );
};
const theme = createTheme({
    overrides: {
        MuiCssBaseline: {
            '@global': {
                body: {
                    overflow: 'hidden',
                },
            },
        },
    },
});

export default App;
