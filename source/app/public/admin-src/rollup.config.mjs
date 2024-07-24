import terser from '@rollup/plugin-terser';
import commonjs from '@rollup/plugin-commonjs';
import nodeResolve from '@rollup/plugin-node-resolve';
import replace from '@rollup/plugin-replace';

export default {
  input: 'main.js',
  context: 'window',
  output: [
    {
      file: '../admin/js/bundle.js',
      format: 'esm',
    },
    {
      file: '../admin/js/bundle.min.js',
      format: 'esm',
      plugins: [terser()],
    },
  ],
  plugins: [
    nodeResolve(),
    commonjs(),
    replace({
      preventAssignment: true,
      'process.env.NODE_ENV': JSON.stringify('production'),
    }),
  ]
};
