import axios from "axios"

const axiosClient = axios.create({
    //baseURL:`${import.meta.env.VITE_API_BASE_URL}/api`
    baseURL:'http://localhost:8000/api'
})

export class ShiftsApiService {

    /**General GET Method */
    getShifts() {
        return axiosClient.get('/shifts')
    }

    /**General ID GET Method */
    getShiftById(s_id) {
        return axiosClient.get('/shifts/'+s_id)
    }

    /**Specific GET Method */
    getShiftsByMovie(m_id) {
        return axiosClient.get('/movies/'+m_id+'/shifts/')
    }

    /**Specific ID GET Method */
    getShiftsByMovieById(m_id,s_id) {
        return axiosClient.get('/movies/'+m_id+'/shifts/'+s_id)
    }

    /**General POST Method */
    createShift(body) {
        return axiosClient.post('/shifts',body)
    }

    /**Specific ID POST Method */
    createShift(m_id,body) {
        return axiosClient.post('/movies/'+m_id+'/shifts/',body)
    }

    /**PUT Method */
    updateShift(id,body) {
        return axiosClient.put('/shifts/'+id,body)
    }

    /**DELETE Method */
    deleteShift(id) {
        return axiosClient.delete('/shifts/'+id)
    }
}