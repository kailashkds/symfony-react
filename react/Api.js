import axios from 'axios';
import {toastShow} from "./layout/toast";
import {useNavigate} from "react-router-dom";

export const createUser = async (payload) => {
    const { data } = await axios.post(`/api/user/create`, payload)
    toastShow(data.response,data.message);
}
export const createNotes = async (payload,token) => {
    const { data } = await axios.post(`/api/notes/create`, payload,{
        headers: {
            Authorization: `Bearer ${token}`,
        }
    })
    toastShow(data.response,data.message);
}

export const loginUser = async (payload) => {
    const { data } = await axios.post(`/api/login`, payload)
        .then(response => {
            const token  =  response.data.token;
            localStorage.setItem("token", token);
            setAuthToken(token);
            window.location.href = '/notes';
        })
        .catch(err => {
            toastShow("error",err.response.data.message);
        });
    toastShow("success",data.message);
    return data
}

export const logout = async (payload) => {
    localStorage.removeItem("token");
}

export const getListOfCategories = async (token) => {
    const { data } = await axios.get(`/api/category/list`,{
        headers: {
            Authorization: `Bearer ${token}`,
        }
    })

    return data.data
}

export const getListOfNotes = async (token) => {
    const { data } = await axios.get(`/api/notes/list`,{
        headers: {
            Authorization: `Bearer ${token}`,
        }
    })
    toastShow(data.response,data.message);
    if(data.response === 'error')
    {
        window.location.href = '/login';
    }
    return data.data
}
export const filterRows = async (token,type,value) => {
    const { data } = await axios.get(`/api/notes/filter?${type}=${value}`,{
        headers: {
            Authorization: `Bearer ${token}`,
        }
    })
    toastShow(data.response,data.message);
    return data.data
}



export const setAuthToken = token => {
    if (token) {
        axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
    }
    else
        delete axios.defaults.headers.common["Authorization"];
}

