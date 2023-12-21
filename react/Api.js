import axios from 'axios';

export const createUser = async (payload) => {
    const { data } = await axios.post(`/user/create`, payload)
    return data
}