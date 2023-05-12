import axios from "axios"

const axiosClient = axios.create({
    //baseURL:`${import.meta.env.VITE_API_BASE_URL}/api`
    baseURL:'http://localhost:8000/api'
})

export class MoviesApiService {

    /**GET Method */
    getMovies() {
        return axiosClient.get('/movies')
    }

    /**GET Method */
    getMovieById(id) {
        return axiosClient.get('/movies/'+id)
    }

    /**POST Method */
    createMovie(body) {
        return axiosClient.post('/movies',body)
    }

    /**PUT Method */
    updateMovie(id,body) {
        return axiosClient.put('/movies/'+id,body)
    }

    /**DELETE Method */
    deleteMovie(id) {
        return axiosClient.delete('/movies/'+id)
    }
}